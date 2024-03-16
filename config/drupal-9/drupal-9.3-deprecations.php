<?php

declare(strict_types=1);

use DrupalRector\Drupal9\Rector\Deprecation\ExtensionPathRector;
use DrupalRector\Drupal9\Rector\Deprecation\FileBuildUriRector;
use DrupalRector\Drupal9\Rector\Deprecation\FunctionToEntityTypeStorageMethod;
use DrupalRector\Drupal9\Rector\Deprecation\FunctionToFirstArgMethodRector;
use DrupalRector\Drupal9\Rector\Deprecation\SystemSortByInfoNameRector;
use DrupalRector\Drupal9\Rector\Deprecation\TaxonomyTermLoadMultipleByNameRector;
use DrupalRector\Drupal9\Rector\Deprecation\TaxonomyVocabularyGetNamesDrupalStaticResetRector;
use DrupalRector\Drupal9\Rector\Deprecation\TaxonomyVocabularyGetNamesRector;
use DrupalRector\Drupal9\Rector\ValueObject\ExtensionPathConfiguration;
use DrupalRector\Drupal9\Rector\ValueObject\FunctionToEntityTypeStorageConfiguration;
use DrupalRector\Drupal9\Rector\ValueObject\FunctionToFirstArgMethodConfiguration;
use DrupalRector\Rector\Deprecation\ConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\ConstantToClassConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use DrupalRector\Services\AddCommentService;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, function () {
        return new AddCommentService();
    });
    // Change record: https://www.drupal.org/node/2940438.
    $rectorConfig->ruleWithConfiguration(ExtensionPathRector::class, [
        new ExtensionPathConfiguration('drupal_get_filename', 'getPathname'),
        new ExtensionPathConfiguration('drupal_get_path', 'getPath'),
    ]);

    // Change record: https://www.drupal.org/node/2940031
    $rectorConfig->rule(DrupalRector\Drupal9\Rector\Deprecation\FileCreateUrlRector::class);
    $rectorConfig->rule(DrupalRector\Drupal9\Rector\Deprecation\FileUrlTransformRelativeRector::class);
    $rectorConfig->rule(DrupalRector\Drupal9\Rector\Deprecation\FromUriRector::class);

    // Change record: https://www.drupal.org/node/3223520
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('9.3.0', 'file_copy', 'file.repository', 'copy'),
        new FunctionToServiceConfiguration('9.3.0', 'file_move', 'file.repository', 'move'),
        new FunctionToServiceConfiguration('9.3.0', 'file_save_data', 'file.repository', 'writeData'),
        // Change record: https://www.drupal.org/node/2939099
        new FunctionToServiceConfiguration('9.3.0', 'render', 'renderer', 'render'),
    ]);

    // Change record: https://www.drupal.org/node/3223091.
    $rectorConfig->rule(FileBuildUriRector::class);

    // Change record: https://www.drupal.org/node/3225999
    $rectorConfig->rule(SystemSortByInfoNameRector::class);

    // Change rector: https://www.drupal.org/node/3039041
    // Missing: $url = $term->toUrl(); AND $name = taxonomy_term_title($term); AND taxonomy_implode_tags();
    $rectorConfig->ruleWithConfiguration(FunctionToEntityTypeStorageMethod::class, [
        new FunctionToEntityTypeStorageConfiguration('taxonomy_terms_static_reset', 'taxonomy_term', 'resetCache'),
        new FunctionToEntityTypeStorageConfiguration('taxonomy_vocabulary_static_reset', 'taxonomy_vocabulary', 'resetCache'),
    ]);
    $rectorConfig->rule(TaxonomyVocabularyGetNamesRector::class);
    $rectorConfig->rule(TaxonomyTermLoadMultipleByNameRector::class);
    $rectorConfig->rule(TaxonomyVocabularyGetNamesDrupalStaticResetRector::class);
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('9.3.0', 'taxonomy_implode_tags', 'Drupal\Core\Entity\Element\EntityAutocomplete', 'getEntityLabels'),
    ]);
    $rectorConfig->ruleWithConfiguration(FunctionToFirstArgMethodRector::class, [
        new FunctionToFirstArgMethodConfiguration('taxonomy_term_uri', 'toUrl'),
        new FunctionToFirstArgMethodConfiguration('taxonomy_term_title', 'label'),
    ]);

    // Change record: https://www.drupal.org/node/3022147
    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        new ConstantToClassConfiguration(
            'FILE_STATUS_PERMANENT',
            'Drupal\file\FileInterface',
            'STATUS_PERMANENT',
        ),
    ]);
};
