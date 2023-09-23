<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FileBuildUriRector;
use DrupalRector\Rector\Deprecation\FileUrlGenerator;
use DrupalRector\Rector\Deprecation\FunctionToEntityTypeStorageMethod;
use DrupalRector\Rector\Deprecation\FunctionToFirstArgMethodRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\Deprecation\SystemSortByInfoNameRector;
use DrupalRector\Rector\Deprecation\TaxonomyImplodeTagsRector;
use DrupalRector\Rector\Deprecation\TaxonomyTermLoadMultipleByNameRector;
use DrupalRector\Rector\Deprecation\TaxonomyVocabularyGetNamesDrupalStaticResetRector;
use DrupalRector\Rector\Deprecation\TaxonomyVocabularyGetNamesRector;
use DrupalRector\Rector\ValueObject\FunctionToEntityTypeStorageConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToFirstArgMethodConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Rector\ValueObject\ExtensionPathConfiguration;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    // Change record: https://www.drupal.org/node/2940438.
    $rectorConfig->ruleWithConfiguration(\DrupalRector\Rector\Deprecation\ExtensionPathRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        new ExtensionPathConfiguration('drupal_get_filename', 'getPathname'),
        new ExtensionPathConfiguration('drupal_get_path', 'getPath'),
    ]);

    // Change record: https://www.drupal.org/node/2940031
    $rectorConfig->rule(FileUrlGenerator\FileCreateUrlRector::class);
    $rectorConfig->rule(FileUrlGenerator\FileUrlTransformRelativeRector::class);
    $rectorConfig->rule(FileUrlGenerator\FromUriRector::class);

    // Change record: https://www.drupal.org/node/3223520
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('file_copy', 'file.repository', 'copy'),
        new FunctionToServiceConfiguration('file_move', 'file.repository', 'move'),
        new FunctionToServiceConfiguration('file_save_data', 'file.repository', 'writeData'),
        // Change record: https://www.drupal.org/node/2939099
        new FunctionToServiceConfiguration('render', 'renderer', 'render'),
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
    $rectorConfig->rule(TaxonomyImplodeTagsRector::class);
    $rectorConfig->ruleWithConfiguration(FunctionToFirstArgMethodRector::class, [
        new FunctionToFirstArgMethodConfiguration('taxonomy_term_uri', 'toUrl'),
        new FunctionToFirstArgMethodConfiguration('taxonomy_term_title', 'label'),
    ]);
};
