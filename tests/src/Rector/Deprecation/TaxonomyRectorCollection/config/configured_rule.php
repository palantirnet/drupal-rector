<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FunctionToEntityTypeStorageMethod;
use DrupalRector\Rector\ValueObject\FunctionToEntityTypeStorageConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(FunctionToEntityTypeStorageMethod::class, $rectorConfig, FALSE, [
        new FunctionToEntityTypeStorageConfiguration('taxonomy_terms_static_reset', 'taxonomy_term', 'resetCache'),
        new FunctionToEntityTypeStorageConfiguration('taxonomy_vocabulary_static_reset', 'taxonomy_vocabulary', 'resetCache'),
    ]);

    DeprecationBase::addClass(\DrupalRector\Rector\Deprecation\TaxonomyVocabularyGetNamesRector::class, $rectorConfig, FALSE);
};