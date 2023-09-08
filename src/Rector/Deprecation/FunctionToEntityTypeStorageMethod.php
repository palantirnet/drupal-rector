<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\ValueObject\FunctionToEntityTypeStorageConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use PhpParser\Node;
use PhpParser\NodeDumper;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class FunctionToEntityTypeStorageMethod extends AbstractRector implements ConfigurableRectorInterface {

    /**
     * @var array|FunctionToEntityTypeStorageConfiguration[]
     */
    private array $configuration;

    /**
     * @inheritDoc
     */
    public function configure(array $configuration): void {
        foreach ($configuration as $value) {
            if (!($value instanceof FunctionToEntityTypeStorageConfiguration)) {
                throw new \InvalidArgumentException(sprintf(
                    'Each configuration item must be an instance of "%s"',
                    FunctionToEntityTypeStorageConfiguration::class
                ));
            }
        }

        $this->configuration = $configuration;
    }

    public function getRuleDefinition(): RuleDefinition {
        return new RuleDefinition('Refactor function call to an entity storage method',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
        $path = drupal_realpath($path);
        CODE_BEFORE
                    ,
                    <<<'CODE_AFTER'
        $path = \Drupal::service('file_system')
            ->realpath($path);
        CODE_AFTER
                    ,
                    [
                        new FunctionToServiceConfiguration('todo', 'todo', 'todo'),
                    ]
                ),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getNodeTypes(): array {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    public function refactor(Node $node): ?Node {
        assert($node instanceof Node\Expr\FuncCall);

        foreach ($this->configuration as $configuration) {
            if ($this->getName($node->name) !== $configuration->getDeprecatedFunction()) {
                continue;
            }

            $entityTypeManager = $this->nodeFactory->createStaticCall('Drupal', 'entityTypeManager');
            $storageCall = $this->nodeFactory->createMethodCall($entityTypeManager, 'getStorage', $this->nodeFactory->createArgs([$configuration->getEntityTypeId()]));
            $newNode = $this->nodeFactory->createMethodCall($storageCall, $configuration->getEntityStorageMethod(), $node->getArgs());

            return $newNode;
        }

        return null;
    }

}
