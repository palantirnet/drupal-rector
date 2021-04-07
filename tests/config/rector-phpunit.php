<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

/**
 * Assumes drupal/recommended-project is at tests/fixtures/drupal
 *
 * composer create-project drupal/recommended-project:^8.9 tests/fixtures/drupal
 * composer create-project drupal/recommended-project:^9.0 tests/fixtures/drupal
 */
return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ .  '/../../config/drupal-8/drupal-8-all-deprecations.php');

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::AUTOLOAD_PATHS, [
        __DIR__ . '/../fixtures/drupal/web/core',
        __DIR__ . '/../fixtures/drupal/web/core/modules',
        __DIR__ . '/../fixtures/drupal/web/modules',
        __DIR__ . '/../fixtures/drupal/web/profiles'
    ]);
    require_once __DIR__ . '/../fixtures/drupal/web/core/tests/bootstrap.php';
    $parameters->set(Option::SKIP, ['*/upgrade_status/tests/modules/*']);
    $parameters->set(Option::FILE_EXTENSIONS, ['php', 'module', 'theme', 'install', 'profile', 'inc', 'engine']);
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::IMPORT_SHORT_CLASSES, false);
    $parameters->set(Option::IMPORT_DOC_BLOCKS, false);

    $parameters->set('drupal_rector_notices_as_comments', true);
};
