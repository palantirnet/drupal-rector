<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated locale batch procedural functions from locale.batch.inc,
 * locale.bulk.inc, and locale.compare.inc with equivalent methods on the
 * LocaleFetch, LocaleImportBatch, LocaleConfigBatch, and LocaleProjectChecker
 * services.
 *
 * Each deprecated function delegates directly to the service method with
 * identical arguments, so the rewrite is a mechanical 1-to-1 mapping.
 *
 * locale_config_batch_build() and locale_translation_batch_status_build() are
 * intentionally NOT handled: the former changed its argument signature and the
 * latter changed its behavior (the replacement calls batch_set() immediately
 * rather than returning the array). Those two require manual migration.
 *
 * Deprecated in drupal:11.4.0 and removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3581303
 * @see https://www.drupal.org/node/3589759 (change record)
 */
class ReplaceLocaleBatchProceduralFunctionsRector extends AbstractDrupalCoreRector
{
    /**
     * PHPStan deprecation messages this rector covers (one per function).
     *
     * @var string[]
     */
    public const PHPSTAN_MESSAGES = [
        'Call to deprecated function locale_translation_batch_version_check(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleFetch::class)->batchVersionCheck() instead.',
        'Call to deprecated function locale_translation_batch_status_check(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleFetch::class)->batchStatusCheck() instead.',
        'Call to deprecated function locale_translation_batch_fetch_download(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleFetch::class)->batchDownload() instead.',
        'Call to deprecated function locale_translation_batch_fetch_import(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleFetch::class)->batchImport() instead.',
        'Call to deprecated function locale_translation_batch_fetch_finished(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleFetch::class)->batchFinished() instead.',
        'Call to deprecated function _locale_translation_batch_status_operations(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleFetch::class)->getStatusOperations() instead.',
        'Call to deprecated function locale_translate_batch_build(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleImportBatch::class)->buildBatch() instead.',
        'Call to deprecated function locale_translate_batch_import(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleImportBatch::class)->batchImport() instead.',
        'Call to deprecated function locale_translate_batch_import_save(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleImportBatch::class)->batchSave() instead.',
        'Call to deprecated function locale_translate_batch_refresh(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleImportBatch::class)->batchRefresh() instead.',
        'Call to deprecated function locale_translate_batch_finished(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleImportBatch::class)->batchFinished() instead.',
        'Call to deprecated function locale_config_batch_update_components(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleConfigBatch::class)->buildBatch() instead.',
        'Call to deprecated function locale_config_batch_update_default_config_langcodes(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleConfigBatch::class)->batchUpdateDefaultConfigLangcodes() instead.',
        'Call to deprecated function locale_config_batch_update_config_translations(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleConfigBatch::class)->batchUpdateConfigTranslations() instead.',
        'Call to deprecated function locale_config_batch_finished(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleConfigBatch::class)->batchFinished() instead.',
        'Call to deprecated function locale_translation_check_projects_batch(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleProjectChecker::class)->triggerBatch() instead.',
        'Call to deprecated function locale_translation_batch_status_finished(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::service(LocaleProjectChecker::class)->batchFinished() instead.',
    ];

    /**
     * Maps each deprecated function name to [service FQCN, method name].
     *
     * @var array<string, array{string, string}>
     */
    private const FUNCTION_MAP = [
        // locale.batch.inc
        'locale_translation_batch_version_check' => ['Drupal\locale\LocaleFetch', 'batchVersionCheck'],
        'locale_translation_batch_status_check' => ['Drupal\locale\LocaleFetch', 'batchStatusCheck'],
        'locale_translation_batch_fetch_download' => ['Drupal\locale\LocaleFetch', 'batchDownload'],
        'locale_translation_batch_fetch_import' => ['Drupal\locale\LocaleFetch', 'batchImport'],
        'locale_translation_batch_fetch_finished' => ['Drupal\locale\LocaleFetch', 'batchFinished'],
        '_locale_translation_batch_status_operations' => ['Drupal\locale\LocaleFetch', 'getStatusOperations'],
        // locale.bulk.inc
        'locale_translate_batch_build' => ['Drupal\locale\LocaleImportBatch', 'buildBatch'],
        'locale_translate_batch_import' => ['Drupal\locale\LocaleImportBatch', 'batchImport'],
        'locale_translate_batch_import_save' => ['Drupal\locale\LocaleImportBatch', 'batchSave'],
        'locale_translate_batch_refresh' => ['Drupal\locale\LocaleImportBatch', 'batchRefresh'],
        'locale_translate_batch_finished' => ['Drupal\locale\LocaleImportBatch', 'batchFinished'],
        'locale_config_batch_update_components' => ['Drupal\locale\LocaleConfigBatch', 'buildBatch'],
        'locale_config_batch_update_default_config_langcodes' => ['Drupal\locale\LocaleConfigBatch', 'batchUpdateDefaultConfigLangcodes'],
        'locale_config_batch_update_config_translations' => ['Drupal\locale\LocaleConfigBatch', 'batchUpdateConfigTranslations'],
        'locale_config_batch_finished' => ['Drupal\locale\LocaleConfigBatch', 'batchFinished'],
        // locale.compare.inc
        'locale_translation_check_projects_batch' => ['Drupal\locale\LocaleProjectChecker', 'triggerBatch'],
        'locale_translation_batch_status_finished' => ['Drupal\locale\LocaleProjectChecker', 'batchFinished'],
    ];

    /** @var DrupalIntroducedVersionConfiguration[] */
    protected array $configuration;

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalIntroducedVersionConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalIntroducedVersionConfiguration::class));
            }
        }
        parent::configure($configuration);
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof FuncCall);

        if (!$node->name instanceof Name) {
            return null;
        }

        $functionName = $node->name->toString();
        if (!isset(self::FUNCTION_MAP[$functionName])) {
            return null;
        }

        [$serviceClass, $method] = self::FUNCTION_MAP[$functionName];

        $serviceCall = new StaticCall(
            new FullyQualified('Drupal'),
            'service',
            [new Arg(new ClassConstFetch(new FullyQualified($serviceClass), 'class'))]
        );

        return new MethodCall($serviceCall, $method, $node->args);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated locale batch procedural functions with LocaleFetch, LocaleImportBatch, LocaleConfigBatch, and LocaleProjectChecker service methods.',
            [
                new ConfiguredCodeSample(
                    'locale_translate_batch_build($files, $options);',
                    '\Drupal::service(\Drupal\locale\LocaleImportBatch::class)->buildBatch($files, $options);',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
                new ConfiguredCodeSample(
                    'locale_config_batch_update_components($options, $langcodes, $components, $update_default_config_langcodes);',
                    '\Drupal::service(\Drupal\locale\LocaleConfigBatch::class)->buildBatch($options, $langcodes, $components, $update_default_config_langcodes);',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
                new ConfiguredCodeSample(
                    'locale_translation_check_projects_batch($projects, $langcodes);',
                    '\Drupal::service(\Drupal\locale\LocaleProjectChecker::class)->triggerBatch($projects, $langcodes);',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
            ]
        );
    }
}
