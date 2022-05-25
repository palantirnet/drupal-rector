<?php

function file_functions() {
    \Drupal::service('file.repository')->copy();
    \Drupal::service('file.repository')->move();
    \Drupal::service('file.repository')->writeData();
    $uri1 = \Drupal::service('stream_wrapper_manager')->normalizeUri(\Drupal::config('system.file')->get('default_scheme') . ('://' . 'path/to/file.txt'));
    $path = 'path/to/other/file.png';
    $uri2 = \Drupal::service('stream_wrapper_manager')->normalizeUri(\Drupal::config('system.file')->get('default_scheme') . ('://' . $path));
}
