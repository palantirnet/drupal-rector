<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\BrowserTestBaseGetMockRector;
use DrupalRector\Rector\Deprecation\KernelTestBaseGetMockRector;
use DrupalRector\Rector\Deprecation\UnitTestCaseGetMockRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(UnitTestCaseGetMockRector::class);
    $services->set(KernelTestBaseGetMockRector::class);
    $services->set(BrowserTestBaseGetMockRector::class);

    $parameters = $containerConfigurator->parameters();
    $parameters->set('drupal_rector_notices_as_comments', true);
};
