<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\BrowserTestBaseGetMockRector;
use DrupalRector\Rector\Deprecation\KernelTestBaseGetMockRector;
use DrupalRector\Rector\Deprecation\UnitTestCaseGetMockRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(BrowserTestBaseGetMockRector::class);
    $rectorConfig->rule(KernelTestBaseGetMockRector::class);
    $rectorConfig->rule(UnitTestCaseGetMockRector::class);
};
