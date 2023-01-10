<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertEqualRector;
use DrupalRector\Rector\Deprecation\AssertNotEqualRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;


return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(AssertEqualRector::class, $rectorConfig);
    DeprecationBase::addClass(AssertNotEqualRector::class, $rectorConfig);
};
