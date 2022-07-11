<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertCacheTagRector;
use DrupalRector\Rector\Deprecation\AssertNoCacheTagRector;

return static function (Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(AssertCacheTagRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
    ]);
    $rectorConfig->ruleWithConfiguration(AssertNoCacheTagRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
    ]);

    $parameters = $rectorConfig->parameters();
    $parameters->set('drupal_rector_notices_as_comments', true);
};
