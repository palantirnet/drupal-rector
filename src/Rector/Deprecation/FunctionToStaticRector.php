<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use PhpParser\Node;
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
class FunctionToStaticRector extends AbstractDrupalCoreRector
{
    /**
     * @var array|FunctionToStaticConfiguration[]
     */
    protected array $configuration;

    /**
     * {@inheritdoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof FunctionToStaticConfiguration)) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', FunctionToStaticConfiguration::class));
            }
        }

        parent::configure($configuration);
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof Node\Expr\FuncCall);
        assert($configuration instanceof FunctionToStaticConfiguration);

        if ($this->getName($node) === $configuration->getDeprecatedFunctionName()) {
            $args = $node->getArgs();
            if (count($configuration->getArgumentReorder()) > 0) {
                $origArgs = $node->getArgs();
                foreach ($configuration->getArgumentReorder() as $oldPosition => $newPosition) {
                    $args[$newPosition] = $origArgs[$oldPosition];
                }
            }

            $fullyQualified = new Node\Name\FullyQualified($configuration->getClassName());

            return new Node\Expr\StaticCall($fullyQualified, $configuration->getMethodName(), $args);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated file_directory_os_temp() calls, used in Drupal 8, 9 and 10 deprecations', [
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
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$settings = [];
$filename = 'simple_filename.yaml';
drupal_rewrite_settings($settings, $filename);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$settings = [];
$filename = 'simple_filename.yaml';
SettingsEditor::rewrite($filename, $settings);
CODE_AFTER
                ,
                [
                    new FunctionToStaticConfiguration('10.1.0', 'drupal_rewrite_settings', 'Drupal\Core\Site\SettingsEditor', 'rewrite', [0 => 1, 1 => 0]),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$settings = [];
$filename = 'simple_filename.yaml';
drupal_rewrite_settings($settings, $filename);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$settings = [];
$filename = 'simple_filename.yaml';
SettingsEditor::rewrite($filename, $settings);
CODE_AFTER
                ,
                [
                    new FunctionToStaticConfiguration('10.1.0', 'drupal_rewrite_settings', 'Drupal\Core\Site\SettingsEditor', 'rewrite', [0 => 1, 1 => 0]),
                ]
            ),
        ]);
    }
}
