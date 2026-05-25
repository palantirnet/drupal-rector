<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalServiceRenameConfiguration;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class DrupalServiceRenameRector extends AbstractDrupalCoreRector
{
    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalServiceRenameConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalServiceRenameConfiguration::class));
            }
        }

        parent::configure($configuration);
    }

    public function getNodeTypes(): array
    {
        return [Node\Expr\StaticCall::class];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof Node\Expr\StaticCall);
        assert($configuration instanceof DrupalServiceRenameConfiguration);

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

        if ($argument->value->value !== $configuration->getDeprecatedService()) {
            return null;
        }

        $newNode = clone $node;
        $newNode->args[0] = new Node\Arg(new Node\Scalar\String_($configuration->getNewService()));

        return $newNode;
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
                [new DrupalServiceRenameConfiguration('11.4.0', 'old', 'bar')]
            ),
        ]);
    }
}
