<?php

declare(strict_types=1);

use DrupalRector\Drupal9\Rector\Deprecation\FileCreateUrlRector;
use DrupalRector\Drupal9\Rector\Deprecation\FileUrlTransformRelativeRector;
use DrupalRector\Drupal9\Rector\Deprecation\FromUriRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(FileCreateUrlRector::class, $rectorConfig, false);
    DeprecationBase::addClass(FileUrlTransformRelativeRector::class, $rectorConfig, false);
    DeprecationBase::addClass(FromUriRector::class, $rectorConfig, false);
};
