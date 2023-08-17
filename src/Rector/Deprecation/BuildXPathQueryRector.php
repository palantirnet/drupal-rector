<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class BuildXPathQueryRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'buildXPathQuery';
    protected $methodName = 'buildXPathQuery';

    public function getNodeTypes(): array
    {
        return [
            MethodCall::class,
        ];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::buildXPathQuery() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$xpath = $this->buildXPathQuery('//select[@name=:name]', [':name' => $name]);
$fields = $this->xpath($xpath);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$xpath = $this->assertSession()->buildXPathQuery('//select[@name=:name]', [':name' => $name]);
$fields = $this->xpath($xpath);
CODE_AFTER
            )
        ]);
    }

    protected function createAssertSessionMethodCall(string $method, array $args): Node\Expr\MethodCall
    {
        $assertSessionNode = $this->nodeFactory->createLocalMethodCall('assertSession');
        return $this->nodeFactory->createMethodCall($assertSessionNode, $method, $args);
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\MethodCall);
        if ($this->getName($node->name) !== $this->deprecatedMethodName) {
            return null;
        }
        if ($this->getDeclaringSource($node) !== $this->declaringSource) {
            return null;
        }


        $args = $this->processArgs($node->args);
        if ($this->isAssertSessionMethod) {
            return $this->createAssertSessionMethodCall($this->methodName, $args);
        };

        return $this->nodeFactory->createLocalMethodCall($this->methodName, $args);
    }

    protected function processArgs(array $args): array
    {
        return $args;
    }
}
