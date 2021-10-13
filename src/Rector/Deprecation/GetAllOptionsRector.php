<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Utility\GetDeclaringSourceTrait;
use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeCollector\ScopeResolver\ParentClassScopeResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class GetAllOptionsRector extends AbstractRector
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
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::getAllOptions() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
    $this->drupalGet('/form-test/select');
    $this->assertCount(6, $this->getAllOptions($this->cssSelect('select[name="opt_groups"]')[0]));
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
    $this->drupalGet('/form-test/select');
    $this->assertCount(6, $this->cssSelect('select[name="opt_groups"]')[0]->findAll('xpath', '//option'));
CODE_AFTER
            )
        ]);
    }

    public function getNodeTypes(): array
    {
        return [
            Node\Expr\MethodCall::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\MethodCall);
        if ($this->getName($node->name) !== 'getAllOptions') {
            return null;
        }
        if ($this->getDeclaringSource($node) !== 'Drupal\FunctionalTests\AssertLegacyTrait') {
            return null;
        }

        $args = $node->args;
        $elementNode = $args[0]->value;

        return $this->nodeFactory->createMethodCall(
            $elementNode,
            'findAll',
            $this->nodeFactory->createArgs(['xpath', '//option'])
        );
    }

}
