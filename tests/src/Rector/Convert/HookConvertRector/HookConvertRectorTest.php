<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Rector\Convert\HookConvertRector;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Rector\PhpParser\Printer\BetterStandardPrinter;

class HookConvertRectorTest extends TestCase
{
    private HookConvertRector $rector;

    protected function setUp(): void
    {
        // getLegacyHookFunction() doesn't use the printer; bypassing the
        // constructor avoids rector's internal dependency chain (>=2.4.5
        // ExprAnalyzer now requires NodeNameResolver, which transitively
        // needs ReflectionProvider).
        $printer = (new \ReflectionClass(BetterStandardPrinter::class))->newInstanceWithoutConstructor();
        $this->rector = new HookConvertRector($printer);

        $ref = new \ReflectionClass($this->rector);

        $module = $ref->getProperty('module');
        $module->setAccessible(true);
        $module->setValue($this->rector, 'mymodule');

        $classConst = new Node\Expr\ClassConstFetch(
            new FullyQualified('Drupal\\mymodule\\Hook\\MymoduleHooks'),
            'class'
        );
        $drupalServiceCall = new Node\Expr\StaticCall(
            new FullyQualified('Drupal'),
            'service',
            [new Node\Arg($classConst)]
        );
        $dsc = $ref->getProperty('drupalServiceCall');
        $dsc->setAccessible(true);
        $dsc->setValue($this->rector, $drupalServiceCall);

        $hookClass = $ref->getProperty('hookClass');
        $hookClass->setAccessible(true);
        $hookClass->setValue($this->rector, new Class_(new Identifier('MymoduleHooks')));
    }

    private function parseFunction(string $code): Function_
    {
        $stmts = (new ParserFactory())->createForNewestSupportedVersion()->parse($code);
        foreach ($stmts as $stmt) {
            if ($stmt instanceof Function_) {
                return $stmt;
            }
        }
        throw new \RuntimeException('No function found in code snippet.');
    }

    public function testExplicitVoidReturnTypeGeneratesExpression(): void
    {
        $fn = $this->parseFunction('<?php function mymodule_cache_flush(): void { doWork(); }');
        $result = $this->rector->getLegacyHookFunction($fn);
        $this->assertInstanceOf(Node\Stmt\Expression::class, $result->stmts[0]);
    }

    public function testBareReturnGeneratesExpression(): void
    {
        $fn = $this->parseFunction('<?php function mymodule_page_attachments(array &$page) { if (condition()) { return; } doWork($page); }');
        $result = $this->rector->getLegacyHookFunction($fn);
        $this->assertInstanceOf(Node\Stmt\Expression::class, $result->stmts[0]);
    }

    public function testValueReturnGeneratesReturnStatement(): void
    {
        $fn = $this->parseFunction('<?php function mymodule_node_access($node, $op, $account) { return AccessResult::neutral(); }');
        $result = $this->rector->getLegacyHookFunction($fn);
        $this->assertInstanceOf(Node\Stmt\Return_::class, $result->stmts[0]);
    }

    public function testNoReturnGeneratesExpression(): void
    {
        $fn = $this->parseFunction('<?php function mymodule_user_cancel($edit, $account, $method) { $account->block(); }');
        $result = $this->rector->getLegacyHookFunction($fn);
        $this->assertInstanceOf(Node\Stmt\Expression::class, $result->stmts[0]);
    }

    public function testLegacyHookAttributeIsAdded(): void
    {
        $fn = $this->parseFunction('<?php function mymodule_cache_flush(): void { }');
        $result = $this->rector->getLegacyHookFunction($fn);

        $found = false;
        foreach ($result->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if (str_ends_with((string) $attr->name, 'LegacyHook')) {
                    $found = true;
                }
            }
        }
        $this->assertTrue($found, 'Expected #[LegacyHook] attribute on the function.');
    }

    public function testReturnStatementForwardsMethodCall(): void
    {
        $fn = $this->parseFunction('<?php function mymodule_node_access($node, $op, $account) { return AccessResult::neutral(); }');
        $result = $this->rector->getLegacyHookFunction($fn);

        /** @var Node\Stmt\Return_ $returnStmt */
        $returnStmt = $result->stmts[0];
        $this->assertInstanceOf(Node\Expr\MethodCall::class, $returnStmt->expr);
    }

    public function testExpressionStatementForwardsMethodCall(): void
    {
        $fn = $this->parseFunction('<?php function mymodule_cache_flush(): void { }');
        $result = $this->rector->getLegacyHookFunction($fn);

        /** @var Node\Stmt\Expression $exprStmt */
        $exprStmt = $result->stmts[0];
        $this->assertInstanceOf(Node\Expr\MethodCall::class, $exprStmt->expr);
    }

    private function createMethod(string $code): Node\Stmt\ClassMethod
    {
        $fn = $this->parseFunction($code);
        $method = new \ReflectionMethod($this->rector, 'createMethodFromFunction');
        $method->setAccessible(true);

        return $method->invoke($this->rector, $fn);
    }

    public function testMethodWithoutThisIsDeclaredStatic(): void
    {
        $method = $this->createMethod(<<<'CODE'
<?php
/**
 * Implements hook_user_cancel().
 */
function mymodule_user_cancel($edit, $account, $method) {
    $account->block();
}
CODE);

        $this->assertTrue($method->isStatic(), 'A method that never uses $this should be static.');
    }

    public function testTranslationCallMakesMethodInstanceAndFlagsTrait(): void
    {
        $method = $this->createMethod(<<<'CODE'
<?php
/**
 * Implements hook_user_cancel().
 */
function mymodule_user_cancel($edit, $account, $method) {
    $label = t('Cancelled');
}
CODE);

        // t() became $this->t(), so the method must stay an instance method.
        $this->assertFalse($method->isStatic(), 'A method using $this->t() must not be static.');

        $finder = new \PhpParser\NodeFinder();
        $thisT = $finder->findFirst(
            $method->stmts ?? [],
            fn (Node $n) => $n instanceof Node\Expr\MethodCall
                && $n->var instanceof Node\Expr\Variable
                && $n->var->name === 'this'
                && $n->name instanceof Node\Identifier
                && $n->name->toString() === 't'
        );
        $this->assertNotNull($thisT, 'Expected t() to be rewritten to $this->t().');

        $ref = new \ReflectionProperty($this->rector, 'needsTranslationTrait');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($this->rector), 'StringTranslationTrait should be flagged for the class.');
    }

    public function testFullyQualifiedTranslationCallIsRewritten(): void
    {
        $method = $this->createMethod(<<<'CODE'
<?php
/**
 * Implements hook_user_cancel().
 */
function mymodule_user_cancel($edit, $account, $method) {
    $label = \t('Cancelled');
}
CODE);

        $this->assertFalse($method->isStatic(), '\t() must be rewritten to $this->t() and keep the method instance.');

        $finder = new \PhpParser\NodeFinder();
        $funcCall = $finder->findFirst(
            $method->stmts ?? [],
            fn (Node $n) => $n instanceof Node\Expr\FuncCall
        );
        $this->assertNull($funcCall, 'No bare t()/\t() FuncCall should survive the rewrite.');
    }

    public function testMethodWithoutTranslationDoesNotFlagTrait(): void
    {
        $this->createMethod(<<<'CODE'
<?php
/**
 * Implements hook_user_cancel().
 */
function mymodule_user_cancel($edit, $account, $method) {
    $account->block();
}
CODE);

        $ref = new \ReflectionProperty($this->rector, 'needsTranslationTrait');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($this->rector), 'A hook without t() must not flag StringTranslationTrait.');
    }

    public function testTranslationRewritePreservesArgumentsAcrossMultipleCalls(): void
    {
        $method = $this->createMethod(<<<'CODE'
<?php
/**
 * Implements hook_user_cancel().
 */
function mymodule_user_cancel($edit, $account, $method) {
    $a = t('One', ['@x' => 1]);
    $b = t('Two');
}
CODE);

        $finder = new \PhpParser\NodeFinder();
        $thisCalls = $finder->find(
            $method->stmts ?? [],
            fn (Node $n) => $n instanceof Node\Expr\MethodCall
                && $n->var instanceof Node\Expr\Variable
                && $n->var->name === 'this'
                && $n->name instanceof Node\Identifier
                && $n->name->toString() === 't'
        );
        $this->assertCount(2, $thisCalls, 'Both t() calls should be rewritten to $this->t().');

        // The first call must keep both arguments (text + placeholders array).
        $this->assertCount(2, $thisCalls[0]->args, 'Arguments must be preserved through the rewrite.');
        $firstArg = $thisCalls[0]->args[0]->value;
        $this->assertInstanceOf(Node\Scalar\String_::class, $firstArg);
        $this->assertSame('One', $firstArg->value);
    }

    /**
     * Builds a real printer without the full Rector DI chain.
     *
     * BetterStandardPrinter::__construct() runs the nikic parent constructor
     * (which sets up printing state), so prettyPrintFile() works. Its
     * ExprAnalyzer dependency is only consulted while printing arrays, so it is
     * safe to stub here as long as the asserted method bodies contain none.
     */
    private function realPrinter(): BetterStandardPrinter
    {
        // Indent parameters are normally seeded by the Rector container; set
        // them so the standalone printer can lay out statements.
        \Rector\Configuration\Parameter\SimpleParameterProvider::setParameter(\Rector\Configuration\Option::INDENT_CHAR, ' ');
        \Rector\Configuration\Parameter\SimpleParameterProvider::setParameter(\Rector\Configuration\Option::INDENT_SIZE, 4);

        $exprAnalyzer = (new \ReflectionClass(\Rector\NodeAnalyzer\ExprAnalyzer::class))->newInstanceWithoutConstructor();

        return new BetterStandardPrinter($exprAnalyzer, new \Rector\Util\Reflection\PrivatesAccessor());
    }

    public function testGeneratedHookClassFileIsPrintedCorrectly(): void
    {
        $rector = new HookConvertRector($this->realPrinter());
        // Prevent the destructor from writing the generated file to disk.
        $isDryRun = new \ReflectionProperty($rector, 'isDryRun');
        $isDryRun->setAccessible(true);
        $isDryRun->setValue($rector, true);

        $module = new \ReflectionProperty($rector, 'module');
        $module->setAccessible(true);
        $module->setValue($rector, 'mymodule');

        $hookClass = new \ReflectionProperty($rector, 'hookClass');
        $hookClass->setAccessible(true);
        $class = new Class_(new Identifier('MymoduleHooks'));
        $hookClass->setValue($rector, $class);

        $create = new \ReflectionMethod($rector, 'createMethodFromFunction');
        $create->setAccessible(true);

        // One hook that translates (instance + $this->t()), one that does not
        // (static). Bodies are array-free so the stubbed ExprAnalyzer is unused.
        $class->stmts[] = $create->invoke($rector, $this->parseFunction(<<<'CODE'
<?php
/**
 * Implements hook_user_cancel().
 */
function mymodule_user_cancel($edit, $account, $method) {
    $label = t('Cancelled');
}
CODE));
        $class->stmts[] = $create->invoke($rector, $this->parseFunction(<<<'CODE'
<?php
/**
 * Implements hook_user_login().
 */
function mymodule_user_login($account) {
    $account->block();
}
CODE));

        $build = new \ReflectionMethod($rector, 'buildHookClassStmts');
        $build->setAccessible(true);
        $output = $this->realPrinter()->prettyPrintFile($build->invoke($rector));

        // Imports.
        $this->assertStringContainsString('use Drupal\Core\Hook\Attribute\Hook;', $output);
        $this->assertStringContainsString('use Drupal\Core\StringTranslation\StringTranslationTrait;', $output);
        // Trait used inside the class body.
        $this->assertMatchesRegularExpression('/class MymoduleHooks\s*\{\s*use StringTranslationTrait;/', $output);
        // Translating hook stays an instance method and uses $this->t().
        $this->assertStringContainsString('public function userCancel(', $output);
        $this->assertStringContainsString('$this->t(\'Cancelled\')', $output);
        // Non-translating hook is static.
        $this->assertStringContainsString('public static function userLogin(', $output);
    }
}
