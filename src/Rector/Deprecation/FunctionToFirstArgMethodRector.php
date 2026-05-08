<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\ValueObject\FunctionToFirstArgMethodConfiguration;
use PhpParser\Node;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class FunctionToFirstArgMethodRector extends AbstractRector implements ConfigurableRectorInterface
{
    /** @var FunctionToFirstArgMethodConfiguration[] */
    private array $configuration;

    public function getNodeTypes(): array
    {
        return [Node\Expr\FuncCall::class];
    }

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof FunctionToFirstArgMethodConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', FunctionToFirstArgMethodConfiguration::class));
            }
        }

        $this->configuration = $configuration;
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\FuncCall);

        foreach ($this->configuration as $configuration) {
            if ($this->getName($node) !== $configuration->getDeprecatedFunctionName()) {
                continue;
            }

            $args = $node->getArgs();
            if (count($args) !== 1) {
                continue;
            }

            return $this->nodeFactory->createMethodCall($args[0]->value, $configuration->getMethodName());
        }

        return null;
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
                [new FunctionToFirstArgMethodConfiguration('taxonomy_term_uri', 'toUrl')]
            ),
        ]);
    }
}
