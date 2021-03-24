<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/vendor/palantirnet/drupal-rector/config/drupal-8/drupal-8-all-deprecations.yml');

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::AUTOLOAD_PATHS, ['docroot/core', 'docroot/core/modules', 'docroot/modules', 'docroot/profiles']);
    $parameters->set(Option::SKIP, ['*/upgrade_status/tests/modules/*']);
    $parameters->set(Option::FILE_EXTENSIONS, ['module', 'theme', 'install', 'profile', 'inc', 'engine']);
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::IMPORT_SHORT_CLASSES, false);
    $parameters->set(Option::IMPORT_DOC_BLOCKS, false);

    $parameters->set('drupal_rector_notices_as_comments', true);
};
