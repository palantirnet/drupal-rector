<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertEqualRector;
use DrupalRector\Rector\Deprecation\AssertNotEqualRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(AssertEqualRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertNotEqualRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $parameters = $containerConfigurator->parameters();
    $parameters->set('drupal_rector_notices_as_comments', true);
};
