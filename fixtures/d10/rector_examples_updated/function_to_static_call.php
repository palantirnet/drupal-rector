<?php

use Drupal\Component\Utility\DeprecationHelper;
use Drupal\Core\Site\SettingsEditor;
function simple_example() {
    $settings = [];
    $filename = 'simple_filename.yaml';
    DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => SettingsEditor::rewrite($filename, $settings), fn() => drupal_rewrite_settings($settings, $filename));
}
