<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FileDirectoryOsTempRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(FileDirectoryOsTempRector::class);
};
