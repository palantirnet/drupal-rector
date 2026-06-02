<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Expr\Cast\String_ as CastString_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\TraitUse;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes `use Drupal\Component\Utility\ToStringTrait;` from a class and
 * inserts an inline `public function __toString(): string` that returns
 * `(string) $this->render()`.
 *
 * The trait was a PHP 7.x workaround for fatal errors thrown inside
 * `__toString()`. On PHP 8+ exceptions inside `__toString()` propagate
 * normally, so the trait is deprecated in drupal:11.4.0 and removed in
 * drupal:13.0.0. The replacement is pure PHP and runs on every Drupal
 * version drupal-rector supports — no BC wrapping needed.
 *
 * @see https://www.drupal.org/node/3548957
 * @see https://www.drupal.org/node/3548961
 */
final class RemoveDrupalToStringTraitRector extends AbstractRector
{
    private const TRAIT_FQCN = 'Drupal\Component\Utility\ToStringTrait';

    // PHPStan emits "Usage of deprecated trait …" with the consuming class
    // name embedded ("in class Foo\Bar"). Only the generic suffix below is
    // stable across call sites; upgrade_status's exact-match lookup will
    // need substring/regex handling to cover all consumers.
    public const PHPSTAN_MESSAGES = [
        'Usage of deprecated trait Drupal\Component\Utility\ToStringTrait. Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Implement the __toString() method directly, exception handling is no longer required.',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace use of deprecated Drupal\\Component\\Utility\\ToStringTrait with a direct __toString() method.',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
use Drupal\Component\Utility\ToStringTrait;

class MyClass {
    use ToStringTrait;

    public function render() {
        return 'hello';
    }
}
CODE_BEFORE,
                    <<<'CODE_AFTER'
use Drupal\Component\Utility\ToStringTrait;

class MyClass {
    public function __toString(): string {
        return (string) $this->render();
    }

    public function render() {
        return 'hello';
    }
}
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        $hasToStringTrait = false;

        foreach ($node->stmts as $key => $stmt) {
            if (!$stmt instanceof TraitUse) {
                continue;
            }

            foreach ($stmt->traits as $traitKey => $traitName) {
                if ($this->isName($traitName, self::TRAIT_FQCN)) {
                    $hasToStringTrait = true;
                    unset($stmt->traits[$traitKey]);
                }
            }

            if ($stmt->traits === []) {
                unset($node->stmts[$key]);
            }
        }

        if (!$hasToStringTrait) {
            return null;
        }

        $node->stmts = array_values($node->stmts);

        if ($node->getMethod('__toString') === null) {
            array_unshift($node->stmts, $this->buildToStringMethod());
        }

        return $node;
    }

    private function buildToStringMethod(): ClassMethod
    {
        $method = new ClassMethod(new Identifier('__toString'));
        $method->flags = Modifiers::PUBLIC;
        $method->returnType = new Identifier('string');
        $method->stmts = [
            new Return_(new CastString_(new MethodCall(
                new Variable('this'),
                new Identifier('render')
            ))),
        ];

        return $method;
    }
}
