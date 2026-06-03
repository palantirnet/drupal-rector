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
    $view_config_updater = \Drupal::classResolver(ViewsConfigUpdater::class);
    $view_config_updater->setDeprecationsEnabled(FALSE);
}

function example_fqcn() {
    $updater = \Drupal::classResolver(\Drupal\views\ViewsConfigUpdater::class);
    $updater->setDeprecationsEnabled(FALSE);
}
?>
