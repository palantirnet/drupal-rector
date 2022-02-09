<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\DrupalGetFilenameRector;
use DrupalRector\Rector\Deprecation\DrupalGetPathRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    // Change record: https://www.drupal.org/node/2940438.
    $services->set(DrupalGetPathRector::class);
    $services->set(DrupalGetFilenameRector::class);
};
