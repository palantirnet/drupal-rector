<?php

function file_create_url_example() {
    $uri = 'public://foo.png';
    \Drupal::service('file_url_generator')->generateAbsoluteString($uri);
}

function file_url_transform_relative_example() {
    $uri = 'public://foo.png';
    \Drupal::service('file_url_generator')->transformRelative($uri);
}

function combined_example() {
    $uri = 'public://foo.png';
    \Drupal::service('file_url_generator')->generateString($uri);
}

function from_uri_example() {
    $uri = 'public://foo.png';
    \Drupal::service('file_url_generator')->generate($uri);
}
