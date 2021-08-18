<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeCollector\ScopeResolver\ParentClassScopeResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class GetRawContentRector extends AbstractRector
{

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
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::getRawContent() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->getRawContent()
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->getSession()->getPage()->getContent()
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
        if ($this->getName($node->name) !== 'constructFieldXpath') {
            return null;
        }
        // @todo definitely needs tests on \Drupal\FunctionalJavascriptTests\WebDriverTestBase
        $parentClassName = $this->parentClassScopeResolver->resolveParentClassName($node);
        if ($parentClassName !== 'Drupal\Tests\BrowserTestBase') {
            return null;
        }

        $getSessionNode = $this->nodeFactory->createLocalMethodCall('getSession');
        $getPageNode = $this->nodeFactory->createMethodCall($getSessionNode, 'getPage');
        return $this->nodeFactory->createMethodCall($getPageNode, 'getContent');
    }

}
