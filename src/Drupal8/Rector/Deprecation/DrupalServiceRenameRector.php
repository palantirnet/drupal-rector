<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\Deprecation;

use DrupalRector\Drupal8\Rector\ValueObject\DrupalServiceRenameConfiguration;
use PhpParser\Node;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class DrupalServiceRenameRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var \DrupalRector\Drupal8\Rector\ValueObject\DrupalServiceRenameConfiguration[]
     */
    protected array $staticArgumentRenameConfigs = [];

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof DrupalServiceRenameConfiguration)) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalServiceRenameConfiguration::class));
            }
        }

        $this->staticArgumentRenameConfigs = $configuration;
    }

    public function getNodeTypes(): array
    {
        return [
            Node\Expr\StaticCall::class,
        ];
    }

    public function refactor(Node $node)
    {
        if ($node instanceof Node\Expr\StaticCall) {
            foreach ($this->staticArgumentRenameConfigs as $configuration) {
                if ($this->getName($node->name) === 'service' && (string) $node->class === 'Drupal') {
                    if (count($node->args) === 1) {
                        /* @var Node\Arg $argument */
                        $argument = $node->args[0];

                        if ($argument->value instanceof Node\Scalar\String_ && $argument->value->value === $configuration->getDeprecatedService()) {
                            $node->args[0] = new Node\Arg(new Node\Scalar\String_($configuration->getNewService()));

                            return $node;
                        }
                    }
                }
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
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::service('bar')->foo();
CODE_AFTER
                ,
                [
                    new DrupalServiceRenameConfiguration(
                        'old',
                        'bar',
                    ),
                ]
            ),
        ]);
    }
}
