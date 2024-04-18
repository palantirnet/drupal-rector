<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\Deprecation\VersionedFunctionToServiceRector;
use DrupalRector\Drupal10\Rector\ValueObject\VersionedFunctionToServiceConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(VersionedFunctionToServiceRector::class, $rectorConfig, false, [
        new VersionedFunctionToServiceConfiguration('10.2.0', '_drupal_flush_css_js', 'asset.query_string', 'reset'),
    ]);
};
