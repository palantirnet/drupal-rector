<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use PhpParser\Node;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces function calls to static method calls.
 *
 * Example: \DrupalRector\Rector\Deprecation\FileDirectoryTempOsRector
 *
 * What is covered:
 * - Static replacement
 */
class FunctionToStaticRector extends AbstractDrupalCoreRector implements ConfigurableRectorInterface
{
    /**
     * @var array|FunctionToStaticConfiguration[]
     */
    private array $configuration;

    protected string $version;

    /**
     * @inheritdoc
     */
    public function getNodeTypes(): array {
        return [
            Node\Expr\FuncCall::class
        ];
    }

    public function getVersion(): string {
        return $this->version;
    }

    public function setVersion(string $version): void {
        $this->version = $version;
    }

    public function configure(array $configuration): void {
        foreach ($configuration as $value) {
            if (!($value instanceof FunctionToStaticConfiguration)) {
                throw new \InvalidArgumentException(sprintf(
                    'Each configuration item must be an instance of "%s"',
                    FunctionToStaticConfiguration::class
                ));
            }
        }

        $this->configuration = $configuration;
    }

    /**
     * @inheritdoc
     */
    public function doRefactor(Node $node): ?Node {
        assert($node instanceof Node\Expr\FuncCall);

        foreach ($this->configuration as $configuration) {
            if ($this->getName($node) === $configuration->getDeprecatedFunctionName()) {
                $this->setVersion($configuration->getIntroducedVersion());

                $args = $node->getArgs();
                if (count($configuration->getArgumentReorder()) > 0) {
                    $origArgs = $node->getArgs();
                    foreach ($configuration->getArgumentReorder() as $oldPosition => $newPosition) {
                        $args[$newPosition] = $origArgs[$oldPosition];
                    }
                }

                return new Node\Expr\StaticCall(new Node\Name\FullyQualified($configuration->getClassName()), $configuration->getMethodName(), $args);
            }
        }
        return NULL;
    }


    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated file_directory_os_temp() calls',[
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$dir = file_directory_os_temp();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$dir = \Drupal\Component\FileSystem\FileSystem::getOsTemporaryDirectory();
CODE_AFTER
                ,
                [
                    new FunctionToStaticConfiguration('8.1.0', 'file_directory_os_temp', 'Drupal\Component\FileSystem\FileSystem', 'getOsTemporaryDirectory'),
                ]
            )
        ]);
    }

}
