<?php

declare(strict_types=1);

use DrupalRector\Drupal9\Rector\Deprecation\FunctionToEntityTypeStorageMethod;
use DrupalRector\Drupal9\Rector\Deprecation\FunctionToFirstArgMethodRector;
use DrupalRector\Drupal9\Rector\Deprecation\TaxonomyTermLoadMultipleByNameRector;
use DrupalRector\Drupal9\Rector\Deprecation\TaxonomyVocabularyGetNamesDrupalStaticResetRector;
use DrupalRector\Drupal9\Rector\Deprecation\TaxonomyVocabularyGetNamesRector;
use DrupalRector\Drupal9\Rector\ValueObject\FunctionToEntityTypeStorageConfiguration;
use DrupalRector\Drupal9\Rector\ValueObject\FunctionToFirstArgMethodConfiguration;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(FunctionToEntityTypeStorageMethod::class, $rectorConfig, false, [
        new FunctionToEntityTypeStorageConfiguration('taxonomy_terms_static_reset', 'taxonomy_term', 'resetCache'),
        new FunctionToEntityTypeStorageConfiguration('taxonomy_vocabulary_static_reset', 'taxonomy_vocabulary', 'resetCache'),
    ]);

    DeprecationBase::addClass(TaxonomyVocabularyGetNamesRector::class, $rectorConfig, false);
    DeprecationBase::addClass(TaxonomyTermLoadMultipleByNameRector::class, $rectorConfig, false);
    DeprecationBase::addClass(TaxonomyVocabularyGetNamesDrupalStaticResetRector::class, $rectorConfig, false);

    DeprecationBase::addClass(FunctionToStaticRector::class, $rectorConfig, false, [
        new FunctionToStaticConfiguration('9.3.0', 'taxonomy_implode_tags', 'Drupal\Core\Entity\Element\EntityAutocomplete', 'getEntityLabels'),
    ]);
    DeprecationBase::addClass(FunctionToFirstArgMethodRector::class, $rectorConfig, false, [
        new FunctionToFirstArgMethodConfiguration('taxonomy_term_uri', 'toUrl'),
        new FunctionToFirstArgMethodConfiguration('taxonomy_term_title', 'label'),
    ]);
};
