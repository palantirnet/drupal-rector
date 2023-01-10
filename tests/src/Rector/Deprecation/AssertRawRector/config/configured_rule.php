<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertNoRawRector;
use DrupalRector\Rector\Deprecation\AssertRawRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(AssertRawRector::class, $rectorConfig);
    DeprecationBase::addClass(AssertNoRawRector::class, $rectorConfig);
};
