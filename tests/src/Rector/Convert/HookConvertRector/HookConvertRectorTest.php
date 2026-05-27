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
}
