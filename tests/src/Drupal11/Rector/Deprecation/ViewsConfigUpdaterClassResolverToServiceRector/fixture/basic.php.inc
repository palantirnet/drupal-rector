<?php

use Drupal\views\ViewsConfigUpdater;

function example() {
    $view_config_updater = \Drupal::classResolver(ViewsConfigUpdater::class);
    $view_config_updater->setDeprecationsEnabled(FALSE);
}

function example_fqcn() {
    $updater = \Drupal::classResolver(\Drupal\views\ViewsConfigUpdater::class);
    $updater->setDeprecationsEnabled(FALSE);
}
?>
-----
<?php

use Drupal\views\ViewsConfigUpdater;

function example() {
    $view_config_updater = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ViewsConfigUpdater::class), fn() => \Drupal::classResolver(ViewsConfigUpdater::class));
    $view_config_updater->setDeprecationsEnabled(FALSE);
}

function example_fqcn() {
    $updater = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\views\ViewsConfigUpdater::class), fn() => \Drupal::classResolver(\Drupal\views\ViewsConfigUpdater::class));
    $updater->setDeprecationsEnabled(FALSE);
}
?>
