<?php

function simple_example() {
    $settings = [];
    $filename = 'simple_filename.yaml';
    drupal_rewrite_settings($settings, $filename);
}

/**
 * A simple example.
 */
function simple_example_os_temp() {
    $x = file_directory_os_temp();
}
?>
-----
<?php

function simple_example() {
    $settings = [];
    $filename = 'simple_filename.yaml';
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => drupal_rewrite_settings($settings, $filename), fn() => \Drupal\Core\Site\SettingsEditor::rewrite($filename, $settings));
}

/**
 * A simple example.
 */
function simple_example_os_temp() {
    $x = \Drupal\Component\FileSystem\FileSystem::getOsTemporaryDirectory();
}
?>
