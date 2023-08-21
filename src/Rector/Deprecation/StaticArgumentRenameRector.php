<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\ValueObject\StaticArgumentRenameConfiguration;
use PhpParser\Node;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class StaticArgumentRenameRector extends AbstractRector implements ConfigurableRectorInterface {

    /**
     * @var StaticArgumentRenameConfiguration[] $staticArgumentRenameConfigs
     */
    protected array $staticArgumentRenameConfigs = [];

    public function configure(array $configuration): void {
        foreach ($configuration as $value) {
            if (!($value instanceof StaticArgumentRenameConfiguration)) {
                throw new \InvalidArgumentException(sprintf(
                    'Each configuration item must be an instance of "%s"',
                    StaticArgumentRenameConfiguration::class
                ));
            }
        }

        $this->staticArgumentRenameConfigs = $configuration;
    }

    public function getRuleDefinition(): RuleDefinition {
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
                    new StaticArgumentRenameConfiguration(
                        'old',
                        'bar',
                        'Drupal',
                        'service',
                    )
                ]
            ),
        ]);
    }

    public function getNodeTypes(): array {
        return [
            Node\Expr\StaticCall::class,
        ];
    }

    public function refactor(Node $node) {
        if ($node instanceof Node\Expr\StaticCall) {
            foreach ($this->staticArgumentRenameConfigs as $configuration) {
                if ($this->getName($node->name) === $configuration->getMethodName() && (string) $node->class === $configuration->getFullyQualifiedClassName()) {
                    if (count($node->args) === 1) {
                        /* @var Node\Arg $argument */
                        $argument = $node->args[0];

                        if ($argument->value instanceof Node\Scalar\String_ && $argument->value->value === $configuration->getOldArgument()) {
                            $node->args[0] = new Node\Arg(new Node\Scalar\String_($configuration->getNewArgument()));

                            return $node;
                        }
                    }
                }
            }
        }

        return NULL;
    }

}
