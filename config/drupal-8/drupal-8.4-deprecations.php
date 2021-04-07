<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(\DrupalRector\Rector\Deprecation\BrowserTestBaseGetMockRector::class);
    $services->set(\DrupalRector\Rector\Deprecation\KernelTestBaseGetMockRector::class);
    $services->set(\DrupalRector\Rector\Deprecation\UnitTestCaseGetMockRector::class);
};
