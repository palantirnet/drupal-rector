<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertNoUniqueTextRector;
use DrupalRector\Rector\Deprecation\AssertUniqueTextRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(AssertUniqueTextRector::class, $rectorConfig);
    DeprecationBase::addClass(AssertNoUniqueTextRector::class, $rectorConfig);
};

