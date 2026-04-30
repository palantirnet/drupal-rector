<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\RemoveModuleHandlerAddModuleCallsRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveTwigNodeTransTagArgumentRector;
use DrupalRector\Drupal11\Rector\Deprecation\RenameStopProceduralHookScanRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceEntityOriginalPropertyRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceFileGetContentHeadersRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceLocaleTranslationDefaultServerPatternRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceSessionWritesWithRequestSessionRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceDateTimeRangeConstantsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceAlphadecimalToIntNullRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceFieldgroupToFieldsetRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplacePdoFetchConstantsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceRequirementSeverityConstantsRector;
use DrupalRector\Drupal11\Rector\Deprecation\StatementPrefetchIteratorFetchColumnRector;
use DrupalRector\Rector\Deprecation\ClassConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\FunctionCallRemovalRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\Deprecation\MethodToMethodWithCheckRector;
use DrupalRector\Rector\ValueObject\ClassConstantToClassConstantConfiguration;
use DrupalRector\Rector\ValueObject\FunctionCallRemovalConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3490200
    // StatementPrefetchIterator::fetchColumn() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by fetchField().
    $rectorConfig->rule(StatementPrefetchIteratorFetchColumnRector::class);

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
    // ModuleHandlerInterface::addModule() and addProfile() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // These methods are no-ops and can be removed.
    $rectorConfig->rule(RemoveModuleHandlerAddModuleCallsRector::class);

    // https://www.drupal.org/node/3410938
    // drupal_requirements_severity() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by RequirementSeverity::maxSeverityFromRequirements().
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('11.2.0', 'drupal_requirements_severity', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'maxSeverityFromRequirements'),
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
    $rectorConfig->rule(ReplaceRequirementSeverityConstantsRector::class);

    // https://www.drupal.org/node/3473440
    // TwigNodeTrans 6th $tag constructor argument deprecated in twig/twig 3.12, removed in drupal:11.2.0.
    // Drop the argument.
    $rectorConfig->rule(RemoveTwigNodeTransTagArgumentRector::class);

    // https://www.drupal.org/node/3442810
    // Number::alphadecimalToInt(null/'') deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Both arguments always produced 0; replaced with literal 0.
    $rectorConfig->rule(ReplaceAlphadecimalToIntNullRector::class);

    // https://www.drupal.org/node/3512254
    // #type 'fieldgroup' deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by 'fieldset'.
    $rectorConfig->rule(ReplaceFieldgroupToFieldsetRector::class);

    // https://www.drupal.org/node/3525077
    // PDO::FETCH_* constants deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\Core\Database\Statement\FetchAs enum cases.
    $rectorConfig->rule(ReplacePdoFetchConstantsRector::class);

    // https://www.drupal.org/node/3574901
    // DateTimeRangeConstantsInterface::BOTH/START_DATE/END_DATE deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by DateTimeRangeDisplayOptions enum cases (->value).
    // datetime_type_field_views_data_helper() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by \Drupal::service('datetime.views_helper')->buildViewsData().
    $rectorConfig->rule(ReplaceDateTimeRangeConstantsRector::class);

    // https://www.drupal.org/node/3494126
    // file_get_content_headers($file) deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by $file->getDownloadHeaders().
    $rectorConfig->rule(ReplaceFileGetContentHeadersRector::class);

    // https://www.drupal.org/node/3518527
    // $_SESSION['key'] = $value deprecated in drupal:11.2.0.
    // Replaced by \Drupal::request()->getSession()->set('key', $value).
    $rectorConfig->rule(ReplaceSessionWritesWithRequestSessionRector::class);

    // https://www.drupal.org/node/3571065
    // $entity->original magic property deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Read access replaced by getOriginal(); write access replaced by setOriginal($value).
    $rectorConfig->rule(ReplaceEntityOriginalPropertyRector::class);

    // https://www.drupal.org/node/3477277
    // LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by \Drupal::TRANSLATION_DEFAULT_SERVER_PATTERN.
    $rectorConfig->rule(ReplaceLocaleTranslationDefaultServerPatternRector::class);

    // https://www.drupal.org/node/3495943
    // #[StopProceduralHookScan] attribute renamed to #[ProceduralHookScanStop] in drupal:11.2.0.
    $rectorConfig->rule(RenameStopProceduralHookScanRector::class);

    // https://www.drupal.org/node/3488572
    // Drupal\Core\Entity\Query\Sql\pgsql\* deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Moved to Drupal\pgsql\EntityQuery\*.
    // https://www.drupal.org/node/3472008
    // Drupal\jsonapi\EventSubscriber\ResourceResponseValidator moved to jsonapi_response_validator submodule.
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Drupal\Core\Entity\Query\Sql\pgsql\QueryFactory' => 'Drupal\pgsql\EntityQuery\QueryFactory',
        'Drupal\Core\Entity\Query\Sql\pgsql\Condition' => 'Drupal\pgsql\EntityQuery\Condition',
        'Drupal\jsonapi\EventSubscriber\ResourceResponseValidator' => 'Drupal\jsonapi_response_validator\EventSubscriber\ResourceResponseValidator',
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
