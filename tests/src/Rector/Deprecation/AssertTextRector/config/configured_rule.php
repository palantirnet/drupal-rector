<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertTextRector;
use DrupalRector\Rector\Deprecation\AssertNoTextRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(AssertTextRector::class, $rectorConfig);
    DeprecationBase::addClass(AssertNoTextRector::class, $rectorConfig);
};
