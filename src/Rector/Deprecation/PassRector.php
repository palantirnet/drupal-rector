<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Utility\GetDeclaringSourceTrait;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeCollector\ScopeResolver\ParentClassScopeResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class PassRector extends AbstractRector
{

    use GetDeclaringSourceTrait;

    /**
     * @var ParentClassScopeResolver
     */
    protected $parentClassScopeResolver;

    public function __construct(ParentClassScopeResolver $parentClassScopeResolver)
    {
        $this->parentClassScopeResolver = $parentClassScopeResolver;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::pass() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->pass('The whole transaction is rolled back when a duplicate key insert occurs.');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
CODE_AFTER
            )
        ]);
    }

    public function getNodeTypes(): array
    {
        return [
            Node\Stmt\Expression::class,
        ];
    }

    public function refactor(Node $node)
    {
        assert($node instanceof Node\Stmt\Expression);
        if ($this->getName($node->expr->name) !== 'pass') {
            return null;
        }

        if ($this->getDeclaringSource($node->expr) === 'Drupal\KernelTests\AssertLegacyTrait') {
            return NodeTraverser::REMOVE_NODE;
        }

        return $node;
    }
}
