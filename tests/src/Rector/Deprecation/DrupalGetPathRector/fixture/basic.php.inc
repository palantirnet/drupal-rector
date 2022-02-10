<?php

function simple_example() {
    drupal_get_path('module', 'node');
    drupal_get_path('theme', 'seven');
    drupal_get_path('profile', 'standard');
}
?>
-----
<?php

function simple_example() {
    \Drupal::service('extension.list.module')->getPath('node');
    \Drupal::service('extension.list.theme')->getPath('seven');
    \Drupal::service('extension.list.profile')->getPath('standard');
}
?>
