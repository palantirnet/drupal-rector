<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\RemoveHandlerBaseDefineExtraOptionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveModuleHandlerAddModuleCallsRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveTwigNodeTransTagArgumentRector;
use DrupalRector\Drupal11\Rector\Deprecation\RenameStopProceduralHookScanRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceAlphadecimalToIntNullRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceDateTimeRangeConstantsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceEditorLoadRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceEntityOriginalPropertyRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceFieldgroupToFieldsetRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplacePdoFetchConstantsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceSessionWritesWithRequestSessionRector;
use DrupalRector\Drupal11\Rector\Deprecation\StatementPrefetchIteratorFetchColumnRector;
use DrupalRector\Rector\Deprecation\ClassConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\ConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\FunctionCallRemovalRector;
use DrupalRector\Rector\Deprecation\FunctionToFirstArgMethodRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\Deprecation\MethodToMethodWithCheckRector;
use DrupalRector\Rector\ValueObject\ClassConstantToClassConstantConfiguration;
use DrupalRector\Rector\ValueObject\ConstantToClassConfiguration;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Rector\ValueObject\FunctionCallRemovalConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToFirstArgMethodConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3490200
    // https://www.drupal.org/node/3490312 (change record)
    // StatementPrefetchIterator::fetchColumn() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by fetchField().
    $rectorConfig->ruleWithConfiguration(StatementPrefetchIteratorFetchColumnRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ]);

    // https://www.drupal.org/node/3498947
    // CacheBackendInterface::invalidateAll() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by deleteAll().
    $rectorConfig->ruleWithConfiguration(MethodToMethodWithCheckRector::class, [
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Cache\CacheBackendInterface', 'invalidateAll', 'deleteAll'),
    ]);

    // https://www.drupal.org/node/3501136
    // template_preprocess_*() functions deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by ThemePreprocess and DatePreprocess service methods.
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_time', 'Drupal\Core\Datetime\DatePreprocess', 'preprocessTime'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_datetime_form', 'Drupal\Core\Datetime\DatePreprocess', 'preprocessDatetimeForm'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_datetime_wrapper', 'Drupal\Core\Datetime\DatePreprocess', 'preprocessDatetimeWrapper'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_links', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessLinks'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_container', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessContainer'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_html', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessHtml'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_page', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessPage'),
    ]);

    // https://www.drupal.org/node/3501136
    // template_preprocess() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // https://www.drupal.org/node/3499559
    // update_clear_update_disk_cache(), update_delete_file_if_stale(),
    // _update_manager_cache_directory(), _update_manager_extract_directory(),
    // and _update_manager_unique_identifier() deprecated in drupal:11.2.0, removed in drupal:13.0.0.
    $rectorConfig->ruleWithConfiguration(FunctionCallRemovalRector::class, [
        new FunctionCallRemovalConfiguration('template_preprocess'),
        new FunctionCallRemovalConfiguration('update_clear_update_disk_cache'),
        new FunctionCallRemovalConfiguration('update_delete_file_if_stale'),
        new FunctionCallRemovalConfiguration('_update_manager_cache_directory'),
        new FunctionCallRemovalConfiguration('_update_manager_extract_directory'),
        new FunctionCallRemovalConfiguration('_update_manager_unique_identifier'),
    ]);

    // https://www.drupal.org/node/3528899
    // https://www.drupal.org/node/3550193 (change record)
    // ModuleHandlerInterface::addModule() and addProfile() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // These methods are no-ops and can be removed.
    $rectorConfig->rule(RemoveModuleHandlerAddModuleCallsRector::class);

    // https://www.drupal.org/node/3485084
    // https://www.drupal.org/node/3486781 (change record)
    // HandlerBase::defineExtraOptions() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // No replacement — Drupal core never called it; any override is dead code.
    $rectorConfig->rule(RemoveHandlerBaseDefineExtraOptionsRector::class);

    // https://www.drupal.org/node/3410938
    // drupal_requirements_severity() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by RequirementSeverity::maxSeverityFromRequirements().
    // https://www.drupal.org/node/3495966
    // https://www.drupal.org/node/3497049 (change record)
    // entity_test_create_bundle() and entity_test_delete_bundle() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by EntityTestHelper::createBundle() and EntityTestHelper::deleteBundle().
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('11.2.0', 'drupal_requirements_severity', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'maxSeverityFromRequirements'),
        new FunctionToStaticConfiguration('11.2.0', 'entity_test_create_bundle', 'Drupal\entity_test\EntityTestHelper', 'createBundle'),
        new FunctionToStaticConfiguration('11.2.0', 'entity_test_delete_bundle', 'Drupal\entity_test\EntityTestHelper', 'deleteBundle'),
    ]);

    // https://www.drupal.org/node/3489415
    // views_field_default_views_data() and _views_field_get_entity_type_storage() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by views.field_data_provider service methods.
    // https://www.drupal.org/node/3069442
    // views_entity_field_label() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by entity_field.manager::getFieldLabels().
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.2.0', 'views_field_default_views_data', 'views.field_data_provider', 'defaultFieldImplementation'),
        new FunctionToServiceConfiguration('11.2.0', '_views_field_get_entity_type_storage', 'views.field_data_provider', 'getSqlStorageForField'),
        new FunctionToServiceConfiguration('11.2.0', 'views_entity_field_label', 'entity_field.manager', 'getFieldLabels'),
    ]);

    // https://www.drupal.org/node/3575841
    // REQUIREMENT_INFO/OK/WARNING/ERROR global constants deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by RequirementSeverity enum cases.
    // https://www.drupal.org/node/3477277
    // LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by \Drupal::TRANSLATION_DEFAULT_SERVER_PATTERN.
    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        new ConstantToClassConfiguration('REQUIREMENT_INFO', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'Info'),
        new ConstantToClassConfiguration('REQUIREMENT_OK', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'OK'),
        new ConstantToClassConfiguration('REQUIREMENT_WARNING', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'Warning'),
        new ConstantToClassConfiguration('REQUIREMENT_ERROR', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'Error'),
        new ConstantToClassConfiguration('LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN', 'Drupal', 'TRANSLATION_DEFAULT_SERVER_PATTERN'),
    ]);

    // https://www.drupal.org/node/3473440
    // https://www.drupal.org/node/3474692 (change record)
    // TwigNodeTrans 6th $tag constructor argument deprecated in twig/twig 3.12, removed in drupal:11.2.0.
    // Drop the argument.
    $rectorConfig->ruleWithConfiguration(RemoveTwigNodeTransTagArgumentRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ]);

    // https://www.drupal.org/node/3442810
    // https://www.drupal.org/node/3494472 (change record)
    // Number::alphadecimalToInt(null/'') deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Both arguments always produced 0; replaced with literal 0.
    $rectorConfig->ruleWithConfiguration(ReplaceAlphadecimalToIntNullRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ]);

    // https://www.drupal.org/node/3512254
    // https://www.drupal.org/node/3515272 (change record)
    // #type 'fieldgroup' deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by 'fieldset'.
    $rectorConfig->ruleWithConfiguration(ReplaceFieldgroupToFieldsetRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ]);

    // https://www.drupal.org/node/3525077
    // https://www.drupal.org/node/3488338 (change record)
    // PDO::FETCH_* constants deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\Core\Database\Statement\FetchAs enum cases.
    $rectorConfig->ruleWithConfiguration(ReplacePdoFetchConstantsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ]);

    // https://www.drupal.org/node/3574901
    // DateTimeRangeConstantsInterface::BOTH/START_DATE/END_DATE deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by DateTimeRangeDisplayOptions enum cases (->value).
    // datetime_type_field_views_data_helper() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by \Drupal::service('datetime.views_helper')->buildViewsData().
    $rectorConfig->ruleWithConfiguration(ReplaceDateTimeRangeConstantsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ]);

    // https://www.drupal.org/node/3494126
    // file_get_content_headers($file) deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by $file->getDownloadHeaders().
    $rectorConfig->ruleWithConfiguration(FunctionToFirstArgMethodRector::class, [
        new FunctionToFirstArgMethodConfiguration('11.2.0', 'file_get_content_headers', 'getDownloadHeaders'),
    ]);

    // https://www.drupal.org/node/3518527
    // https://www.drupal.org/node/3518914 (change record)
    // $_SESSION['key'] = $value deprecated in drupal:11.2.0.
    // Replaced by \Drupal::request()->getSession()->set('key', $value).
    $rectorConfig->ruleWithConfiguration(ReplaceSessionWritesWithRequestSessionRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ]);

    // https://www.drupal.org/node/3447794
    // https://www.drupal.org/node/3509245 (change record)
    // editor_load($format_id) deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by entityTypeManager()->getStorage('editor')->load($format_id).
    $rectorConfig->ruleWithConfiguration(ReplaceEditorLoadRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ]);

    // https://www.drupal.org/node/3571065
    // $entity->original magic property deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Read access replaced by getOriginal(); write access replaced by setOriginal($value).
    $rectorConfig->ruleWithConfiguration(ReplaceEntityOriginalPropertyRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ]);

    // https://www.drupal.org/node/3495943
    // #[StopProceduralHookScan] attribute renamed to #[ProceduralHookScanStop] in drupal:11.2.0.
    $rectorConfig->rule(RenameStopProceduralHookScanRector::class);

    // https://www.drupal.org/node/3488572
    // Drupal\Core\Entity\Query\Sql\pgsql\* deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Moved to Drupal\pgsql\EntityQuery\*.
    // https://www.drupal.org/node/3472008
    // Drupal\jsonapi\EventSubscriber\ResourceResponseValidator moved to jsonapi_response_validator submodule.
    // https://www.drupal.org/node/3498915
    // Drupal\migrate_drupal\Plugin\migrate\source\ContentEntity/ContentEntityDeriver deprecated in drupal:11.2.0,
    // removed in drupal:12.0.0. Moved to Drupal\migrate namespace.
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Drupal\Core\Entity\Query\Sql\pgsql\QueryFactory' => 'Drupal\pgsql\EntityQuery\QueryFactory',
        'Drupal\Core\Entity\Query\Sql\pgsql\Condition' => 'Drupal\pgsql\EntityQuery\Condition',
        'Drupal\jsonapi\EventSubscriber\ResourceResponseValidator' => 'Drupal\jsonapi_response_validator\EventSubscriber\ResourceResponseValidator',
        'Drupal\migrate_drupal\Plugin\migrate\source\ContentEntity' => 'Drupal\migrate\Plugin\migrate\source\ContentEntity',
        'Drupal\migrate_drupal\Plugin\migrate\source\ContentEntityDeriver' => 'Drupal\migrate\Plugin\migrate\source\ContentEntityDeriver',
    ]);

    // https://www.drupal.org/node/3575841
    // SystemManager::REQUIREMENT_* deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\Core\Extension\Requirement\RequirementSeverity enum cases.
    $rectorConfig->ruleWithConfiguration(ClassConstantToClassConstantRector::class, [
        new ClassConstantToClassConstantConfiguration(
            'Drupal\system\SystemManager',
            'REQUIREMENT_OK',
            'Drupal\Core\Extension\Requirement\RequirementSeverity',
            'OK',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\system\SystemManager',
            'REQUIREMENT_WARNING',
            'Drupal\Core\Extension\Requirement\RequirementSeverity',
            'Warning',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\system\SystemManager',
            'REQUIREMENT_ERROR',
            'Drupal\Core\Extension\Requirement\RequirementSeverity',
            'Error',
        ),
    ]);
};
