<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\RemoveRootFromConvertDbUrlRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(RemoveRootFromConvertDbUrlRector::class, $rectorConfig, false);
};
