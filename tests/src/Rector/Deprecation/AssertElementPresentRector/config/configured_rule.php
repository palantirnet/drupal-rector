<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertElementNotPresentRector;
use DrupalRector\Rector\Deprecation\AssertElementPresentRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(AssertElementPresentRector::class);
    $services->set(AssertElementNotPresentRector::class);

    $parameters = $containerConfigurator->parameters();
    $parameters->set('drupal_rector_notices_as_comments', true);
};
