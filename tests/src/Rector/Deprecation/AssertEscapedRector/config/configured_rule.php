<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertEscapedRector;
use DrupalRector\Rector\Deprecation\AssertNoEscapedRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(AssertEscapedRector::class, $rectorConfig);
    DeprecationBase::addClass(AssertNoEscapedRector::class, $rectorConfig);
};
