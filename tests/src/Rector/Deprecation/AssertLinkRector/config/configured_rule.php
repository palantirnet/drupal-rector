<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertLinkRector;
use DrupalRector\Rector\Deprecation\AssertNoLinkRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(AssertLinkRector::class, $rectorConfig);
    DeprecationBase::addClass(AssertNoLinkRector::class, $rectorConfig);
};
