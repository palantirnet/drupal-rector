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

function simple_example_file_icon_class() {
    $mime_type = 'application/pdf';
    $classes = [
        'file',
        'file--' . file_icon_class($mime_type),
    ];

    $generic_mime = file_icon_map($mime_type);
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
    $size_literal = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.2.0', fn() => \Drupal\Core\StringTranslation\ByteSizeMarkup::create(81862076662), fn() => format_size(81862076662));
}

function simple_example_file_icon_class() {
    $mime_type = 'application/pdf';
    $classes = [
        'file',
        'file--' . \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.3.0', fn() => \Drupal\file\IconMimeTypes::getIconClass($mime_type), fn() => file_icon_class($mime_type)),
    ];

    $generic_mime = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.3.0', fn() => \Drupal\file\IconMimeTypes::getGenericMimeType($mime_type), fn() => file_icon_map($mime_type));
}
?>
