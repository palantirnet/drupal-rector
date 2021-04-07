<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/vendor/palantirnet/drupal-rector/config/drupal-8/drupal-8-all-deprecations.php');

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::AUTOLOAD_PATHS, ['web/core', 'web/modules', 'web/profiles', 'web/themes']);
    require_once __DIR__ . '/web/core/tests/bootstrap.php';
    $parameters->set(Option::SKIP, ['*/upgrade_status/tests/modules/*']);
    $parameters->set(Option::FILE_EXTENSIONS, ['php', 'module', 'theme', 'install', 'profile', 'inc', 'engine']);
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::IMPORT_SHORT_CLASSES, false);
    $parameters->set(Option::IMPORT_DOC_BLOCKS, false);

    $parameters->set('drupal_rector_notices_as_comments', true);
};
