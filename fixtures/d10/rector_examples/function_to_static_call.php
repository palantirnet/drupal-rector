<?php

function simple_example() {
    $settings = [];
    $filename = 'simple_filename.yaml';
    drupal_rewrite_settings($settings, $filename);
}
