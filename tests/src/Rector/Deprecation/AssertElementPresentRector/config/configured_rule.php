<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertElementNotPresentRector;
use DrupalRector\Rector\Deprecation\AssertElementPresentRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(AssertElementPresentRector::class, $rectorConfig);
    DeprecationBase::addClass(AssertElementNotPresentRector::class, $rectorConfig);
};
