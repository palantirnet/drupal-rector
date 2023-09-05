<?php

namespace DrupalRector\Rector\Deprecation;

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
class FunctionToStaticRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array|FunctionToStaticConfiguration[]
     */
    private array $configuration;

    /**
     * @inheritdoc
     */
    public function getNodeTypes(): array {
        return [
            Node\Expr\FuncCall::class
        ];
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
    public function refactor(Node $node): ?Node {
        foreach ($this->configuration as $configuration) {
            if ($this->getName($node) === $configuration->getDeprecatedFunctionName()) {
                return new Node\Expr\StaticCall(new Node\Name\FullyQualified($configuration->getClassName()), $configuration->getDeprecatedFunctionName());
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
                    new FunctionToStaticConfiguration('file_directory_os_temp', 'Drupal\Component\FileSystem\FileSystem', 'getOsTemporaryDirectory'),
                ]
            )
        ]);
    }

}
