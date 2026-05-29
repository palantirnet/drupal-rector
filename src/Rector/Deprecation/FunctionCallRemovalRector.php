<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\ValueObject\FunctionCallRemovalConfiguration;
use PhpParser\Node;
use PhpParser\NodeVisitor;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes deprecated function call statements with no replacement.
 */
class FunctionCallRemovalRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array|FunctionCallRemovalConfiguration[]
     */
    private array $configuration = [];

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof FunctionCallRemovalConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', FunctionCallRemovalConfiguration::class));
            }
        }

        $this->configuration = array_merge($this->configuration, $configuration);
    }

    public function getNodeTypes(): array
    {
        return [Node\Stmt\Expression::class];
    }

    public function refactor(Node $node): mixed
    {
        assert($node instanceof Node\Stmt\Expression);

        if (!$node->expr instanceof Node\Expr\FuncCall) {
            return null;
        }

        $name = $this->getName($node->expr);
        if ($name === null) {
            return null;
        }

        foreach ($this->configuration as $configuration) {
            if ($name === $configuration->getFunctionName()) {
                return NodeVisitor::REMOVE_NODE;
            }
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Removes deprecated function call statements that have no replacement', [
            new ConfiguredCodeSample(
                'deprecated_function();',
                '',
                [new FunctionCallRemovalConfiguration('deprecated_function')]
            ),
        ]);
    }
}
