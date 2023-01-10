<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertNoPatternRector;
use DrupalRector\Rector\Deprecation\AssertPatternRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(AssertPatternRector::class, $rectorConfig);
    DeprecationBase::addClass(AssertNoPatternRector::class, $rectorConfig);
};
