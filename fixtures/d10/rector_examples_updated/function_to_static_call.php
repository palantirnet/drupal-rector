<?php

use Drupal\Core\Site\SettingsEditor;
function simple_example() {
    $settings = [];
    $filename = 'simple_filename.yaml';
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => drupal_rewrite_settings($settings, $filename), fn() => SettingsEditor::rewrite($filename, $settings));
}
