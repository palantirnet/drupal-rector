<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\DatetimeDateStorageFormatRector;
use DrupalRector\Rector\Deprecation\DatetimeDatetimeStorageFormatRector;
use DrupalRector\Rector\Deprecation\DatetimeStorageTimezoneRector;
use DrupalRector\Rector\Deprecation\DrupalSetMessageRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(DrupalSetMessageRector::class);

    $services->set(DatetimeDateStorageFormatRector::class);

    $services->set(DatetimeDatetimeStorageFormatRector::class);

    $services->set(DatetimeStorageTimezoneRector::class);
};
