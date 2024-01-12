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

function simple_example_format_size() {
    $size_literal = format_size(81862076662);
}
?>
-----
<?php

function simple_example() {
    $settings = [];
    $filename = 'simple_filename.yaml';
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => \Drupal\Core\Site\SettingsEditor::rewrite($filename, $settings), fn() => drupal_rewrite_settings($settings, $filename));
}

/**
 * A simple example.
 */
function simple_example_os_temp() {
    $x = \Drupal\Component\FileSystem\FileSystem::getOsTemporaryDirectory();
}

function simple_example_format_size() {
    $size_literal = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.2.0', fn() => \\Drupal\Core\StringTranslation\ByteSizeMarkup::create(81862076662), fn() => format_size(81862076662));
}
?>
