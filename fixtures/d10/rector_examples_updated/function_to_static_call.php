<?php

function simple_example() {
    $settings = [];
    $filename = 'simple_filename.yaml';
    \Drupal\Core\Site\SettingsEditor::rewrite($filename, $settings);
}
