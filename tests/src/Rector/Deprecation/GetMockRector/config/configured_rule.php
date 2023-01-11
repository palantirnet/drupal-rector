<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\BrowserTestBaseGetMockRector;
use DrupalRector\Rector\Deprecation\KernelTestBaseGetMockRector;
use DrupalRector\Rector\Deprecation\UnitTestCaseGetMockRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(BrowserTestBaseGetMockRector::class, $rectorConfig, FALSE);
    DeprecationBase::addClass(KernelTestBaseGetMockRector::class, $rectorConfig, FALSE);
    DeprecationBase::addClass(UnitTestCaseGetMockRector::class, $rectorConfig, FALSE);
};
