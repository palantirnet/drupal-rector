<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ClearCsrfTokenSeed;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(PHPUnitSetList::PHPUNIT_91);

    $services = $containerConfigurator->services();

    // Change record: https://www.drupal.org/node/3187914
    $services->set(ClearCsrfTokenSeed::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
};
