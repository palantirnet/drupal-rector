<?php

/**
 * Implements hook_install().
 */
function append_file_info_install() {
  require_once DRUPAL_ROOT . '/includes/theme.inc';
  drupal_theme_rebuild();
}

-----
<?php

/**
 * Implements hook_install().
 */
function append_file_info_install() {
  require_once DRUPAL_ROOT . '/includes/theme.inc';
  \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => \Drupal::service('theme.registry')->reset(), fn() => drupal_theme_rebuild());
}

