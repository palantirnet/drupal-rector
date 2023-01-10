<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\RenderRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(RenderRector::class);
};
