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

use Drupal\Core\Site\SettingsEditor;
function simple_example() {
    $settings = [];
    $filename = 'simple_filename.yaml';
    SettingsEditor::rewrite($filename, $settings);
}

/**
 * A simple example.
 */
function simple_example_os_temp() {
    $x = \Drupal\Component\FileSystem\FileSystem::getOsTemporaryDirectory();
}
?>
