<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\FunctionToFirstArgMethodConfiguration;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class FunctionToFirstArgMethodRector extends AbstractDrupalCoreRector
{
    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof FunctionToFirstArgMethodConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', FunctionToFirstArgMethodConfiguration::class));
            }
        }

        parent::configure($configuration);
    }

    public function getNodeTypes(): array
    {
        return [Node\Expr\FuncCall::class];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof Node\Expr\FuncCall);
        assert($configuration instanceof FunctionToFirstArgMethodConfiguration);

        if ($this->getName($node) !== $configuration->getDeprecatedFunctionName()) {
            return null;
        }

        $args = $node->getArgs();
        if (count($args) !== 1) {
            return null;
        }

        return $this->nodeFactory->createMethodCall($args[0]->value, $configuration->getMethodName());
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replaces function calls where the first argument is an object with a method call on that object', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$url = taxonomy_term_uri($term);
CODE_BEFORE,
                <<<'CODE_AFTER'
$url = $term->toUrl();
CODE_AFTER,
                [new FunctionToFirstArgMethodConfiguration('9.3.0', 'taxonomy_term_uri', 'toUrl')]
            ),
        ]);
    }
}
