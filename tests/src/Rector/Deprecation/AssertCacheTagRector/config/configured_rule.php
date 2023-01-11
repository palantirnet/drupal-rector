<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertCacheTagRector;
use DrupalRector\Rector\Deprecation\AssertNoCacheTagRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(AssertCacheTagRector::class, $rectorConfig);
    DeprecationBase::addClass(AssertNoCacheTagRector::class, $rectorConfig);
};
