<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\DatetimeStorageTimezoneRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(DatetimeStorageTimezoneRector::class);
    $parameters = $containerConfigurator->parameters();
    $parameters->set('drupal_rector_notices_as_comments', true);
};
