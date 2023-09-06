<?php

use Drupal\Core\Site\SettingsEditor;
function simple_example() {
    $settings = [];
    $filename = 'simple_filename.yaml';
    SettingsEditor::rewrite($filename, $settings);
}
