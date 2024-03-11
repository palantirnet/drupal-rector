<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated function call with service method call.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
class FunctionToServiceRector extends AbstractDrupalCoreRector
{
    /**
     * @var FunctionToServiceConfiguration[]
     */
    protected array $configuration;

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof FunctionToServiceConfiguration)) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', FunctionToServiceConfiguration::class));
            }
        }

        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($configuration instanceof FunctionToServiceConfiguration);
        assert($node instanceof Node\Expr\FuncCall);

        if ($this->getName($node->name) === $configuration->getDeprecatedFunctionName()) {
            // This creates a service call like `\Drupal::service('file_system').
            $service = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'service', [new Node\Arg(new Node\Scalar\String_($configuration->getServiceName()))]);

            $method_name = new Node\Identifier($configuration->getServiceMethodName());

            return new Node\Expr\MethodCall($service, $method_name, $node->args);
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated function to service calls, used in Drupal 8 and 9 deprecations', [
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
                    new FunctionToServiceConfiguration('8.0.0', 'drupal_realpath', 'file_system', 'realpath'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$result = drupal_render($elements);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$result = \Drupal::service('renderer')->render($elements);
CODE_AFTER
                ,
                [
                    new FunctionToServiceConfiguration('8.0.0', 'drupal_render', 'renderer', 'render'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$result = drupal_render_root($elements);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$result = \Drupal::service('renderer')->renderRoot($elements);
CODE_AFTER
                ,
                [
                    new FunctionToServiceConfiguration('8.0.0', 'drupal_render_root', 'renderer', 'renderRoot'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$display = entity_get_display($entity_type, $bundle, $view_mode)
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$display = \Drupal::service('entity_display.repository')
    ->getViewDisplay($entity_type, $bundle, $view_mode);
CODE_AFTER
                ,
                [
                    new FunctionToServiceConfiguration('8.8.0', 'entity_get_display', 'entity_display.repository', 'getViewDisplay'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$display = entity_get_form_display($entity_type, $bundle, $form_mode)
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$display = \Drupal::service('entity_display.repository')
    ->getFormDisplay($entity_type, $bundle, $form_mode);
CODE_AFTER
                ,
                [
                    new FunctionToServiceConfiguration('8.8.0', 'entity_get_form_display', 'entity_display.repository', 'getFormDisplay'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
file_copy();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::service('file.repository')->copy();
CODE_AFTER
                ,
                [
                    new FunctionToServiceConfiguration('9.3.0', 'file_copy', 'file.repository', 'copy'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$dir = file_directory_temp();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$dir = \Drupal::service('file_system')->getTempDirectory();
CODE_AFTER
                ,
                [
                    new FunctionToServiceConfiguration('8.0.0', 'file_directory_temp', 'file_system', 'getTempDirectory'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
file_move();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::service('file.repository')->move();
CODE_AFTER
                ,
                [
                    new FunctionToServiceConfiguration('9.3.0', 'file_move', 'file.repository', 'move'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$result = file_prepare_directory($directory, $options);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$result = \Drupal::service('file_system')->prepareDirectory($directory, $options);
CODE_AFTER
                ,
                [
                    new FunctionToServiceConfiguration('8.7.0', 'file_prepare_directory', 'file_system', 'prepareDirectory'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
file_save_data($data);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::service('file.repository')->writeData($data);
CODE_AFTER
                ,
                [
                    new FunctionToServiceConfiguration('8.7.0', 'file_save_data', 'file.repository', 'writeData'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$files = file_scan_directory($directory);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$files = \Drupal::service('file_system')->scanDirectory($directory);
CODE_AFTER
                ,
                [
                    new FunctionToServiceConfiguration('8.8.0', 'file_scan_directory', 'file_system', 'scanDirectory'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$result = file_unmanaged_save_data($data, $destination, $replace);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$result = \Drupal::service('file_system')->saveData($data, $destination, $replace);
CODE_AFTER
                ,
                [
                    new FunctionToServiceConfiguration('9.3.0', 'file_unmanaged_save_data', 'file_system', 'saveData'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$result = file_uri_target($uri)
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$result = \Drupal::service('stream_wrapper_manager')->getTarget($uri);
CODE_AFTER
                ,
                [
                    new FunctionToServiceConfiguration('8.8.0', 'file_uri_target', 'stream_wrapper_manager', 'getTarget'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$date = format_date($timestamp, $type, $format, $timezone, $langcode);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$date = \Drupal::service('date.formatter')->format($timestamp, $type, $format, $timezone, $langcode);
CODE_AFTER
                ,
                [
                    new FunctionToServiceConfiguration('8.0.0', 'format_date', 'date.formatter', 'format'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$date = format_date($timestamp, $type, $format, $timezone, $langcode);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$date = \Drupal::service('date.formatter')->format($timestamp, $type, $format, $timezone, $langcode);
CODE_AFTER
                ,
                [
                    new FunctionToServiceConfiguration('8.0.0', 'format_date', 'date.formatter', 'format'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$output = render($build);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$output = \Drupal::service('renderer')->render($build);
CODE_AFTER
                ,
                [
                    new FunctionToServiceConfiguration('9.3.0', 'render', 'renderer', 'render'),
                ]
            ),
        ]);
    }
}
