<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\ValueObject\DrupalServiceRenameConfiguration;
use PhpParser\Node;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class DrupalServiceRenameRector extends AbstractRector implements ConfigurableRectorInterface
{
    /** @var DrupalServiceRenameConfiguration[] */
    protected array $configuration = [];

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalServiceRenameConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalServiceRenameConfiguration::class));
            }
        }

        $this->configuration = $configuration;
    }

    public function getNodeTypes(): array
    {
        return [Node\Expr\StaticCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\StaticCall);

        if ($this->getName($node->name) !== 'service' || (string) $node->class !== 'Drupal') {
            return null;
        }

        if (count($node->args) !== 1) {
            return null;
        }

        $argument = $node->args[0];
        if (!$argument instanceof Node\Arg || !$argument->value instanceof Node\Scalar\String_) {
            return null;
        }

        foreach ($this->configuration as $config) {
            if ($argument->value->value === $config->getDeprecatedService()) {
                $node->args[0] = new Node\Arg(new Node\Scalar\String_($config->getNewService()));

                return $node;
            }
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Renames the IDs in Drupal::service() calls', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
\Drupal::service('old')->foo();
CODE_BEFORE,
                <<<'CODE_AFTER'
\Drupal::service('bar')->foo();
CODE_AFTER,
                [new DrupalServiceRenameConfiguration('old', 'bar')]
            ),
        ]);
    }
}
