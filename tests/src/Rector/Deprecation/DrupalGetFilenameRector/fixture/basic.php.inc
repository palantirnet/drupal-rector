<?php

function simple_example() {
    drupal_get_filename('module', 'node');
    drupal_get_filename('theme', 'seven');
    drupal_get_filename('profile', 'standard');
}
?>
-----
<?php

function simple_example() {
    \Drupal::service('extension.list.module')->getPathname('node');
    \Drupal::service('extension.list.theme')->getPathname('seven');
    \Drupal::service('extension.list.profile')->getPathname('standard');
}
?>
