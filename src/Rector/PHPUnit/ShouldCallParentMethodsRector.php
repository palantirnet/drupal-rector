<?php

declare(strict_types=1);

namespace DrupalRector\Rector\PHPUnit;

use PhpParser\Node;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ShouldCallParentMethodsRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [
            Node\Stmt\ClassMethod::class,
        ];
    }

    /**
     * @phpstan-param Node\Stmt\ClassMethod $node
     *
     * @param Node $node
     *
     * @return Node|null
     */
    public function refactor(Node $node)
    {
        if (class_exists('Rector\PHPStan\ScopeFetcher')) {
            $scope = ScopeFetcher::fetch($node);
        } else {
            $scope = $node->getAttribute('scope');
        }
        if ($scope->getClassReflection() === null) {
            return null;
        }

        if (!$scope->getClassReflection()->isSubclassOf(\PHPUnit\Framework\TestCase::class)) {
            return null;
        }

        $parentClass = $scope->getClassReflection()->getParentClass();

        if ($parentClass === null) {
            return null;
        }

        if (!in_array(strtolower($node->name->name), ['setup', 'teardown'], true)) {
            return null;
        }

        $hasParentCall = $this->hasParentClassCall($node->getStmts());

        if ($hasParentCall === false) {
            $expr = new Node\Stmt\Expression(
                new Node\Expr\StaticCall(
                    new Node\Name('parent'),
                    $node->name->name
                )
            );

            $node->stmts = array_merge([$expr], $node->stmts);

            return $node;
        }

        return null;
    }

    /**
     * @param Node\Stmt[]|null $stmts
     *
     * @return bool
     */
    private function hasParentClassCall(?array $stmts): bool
    {
        if ($stmts === null) {
            return false;
        }

        foreach ($stmts as $stmt) {
            if (!$stmt instanceof Node\Stmt\Expression) {
                continue;
            }

            if (!$stmt->expr instanceof Node\Expr\StaticCall) {
                continue;
            }

            if (!$stmt->expr->class instanceof Node\Name) {
                continue;
            }

            $class = (string) $stmt->expr->class;

            if (strtolower($class) !== 'parent') {
                continue;
            }

            if (!$stmt->expr->name instanceof Node\Identifier) {
                continue;
            }

            if (in_array(strtolower($stmt->expr->name->name), ['setup', 'teardown'], true)) {
                return true;
            }
        }

        return false;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('PHPUnit based tests should call parent methods (setUp, tearDown)',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
namespace Drupal\Tests\Rector\Deprecation\PHPUnit\ShouldCallParentMethodsRector\fixture;

use Drupal\KernelTests\KernelTestBase;

final class SetupVoidTest extends KernelTestBase {

    protected function setUp(): void
    {
        $test = 'doing things';
    }

    protected function tearDown(): void
    {
        $test = 'doing things';
    }

}
CODE_BEFORE
                    ,
                    <<<'CODE_SAMPLE'
namespace Drupal\Tests\Rector\Deprecation\PHPUnit\ShouldCallParentMethodsRector\fixture;

use Drupal\KernelTests\KernelTestBase;

final class SetupVoidTest extends KernelTestBase {

    protected function setUp(): void
    {
        parent::setUp();
        $test = 'doing things';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $test = 'doing things';
    }

}
CODE_SAMPLE
                ),
            ]
        );
    }
}
