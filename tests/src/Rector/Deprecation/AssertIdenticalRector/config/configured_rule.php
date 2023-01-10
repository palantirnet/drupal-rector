<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertIdenticalObjectRector;
use DrupalRector\Rector\Deprecation\AssertIdenticalRector;
use DrupalRector\Rector\Deprecation\AssertNotIdenticalRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(AssertIdenticalObjectRector::class, $rectorConfig);
    DeprecationBase::addClass(AssertIdenticalRector::class, $rectorConfig);
    DeprecationBase::addClass(AssertNotIdenticalRector::class, $rectorConfig);
};
