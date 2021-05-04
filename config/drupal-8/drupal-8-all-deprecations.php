<?php

declare(strict_types=1);

use DrupalRector\Twig\Transformer\TwigReplaceTransformer;
use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/drupal-8.*');

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::BOOTSTRAP_FILES, [
        __DIR__ . '/../drupal-phpunit-bootstrap-file.php'
    ]);

    $services = $containerConfigurator->services();
    $services->load('DrupalRector\\', __DIR__ . '/../../src');
    $services->set(TwigReplaceTransformer::class);
};
