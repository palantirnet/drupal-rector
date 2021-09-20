<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/drupal-8.*');

    $containerConfigurator->import(PHPUnitSetList::PHPUNIT_60);
    $containerConfigurator->import(PHPUnitSetList::PHPUNIT_70);
    $containerConfigurator->import(PHPUnitSetList::PHPUNIT_75);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::BOOTSTRAP_FILES, [
        __DIR__ . '/../drupal-phpunit-bootstrap-file.php'
    ]);
};
