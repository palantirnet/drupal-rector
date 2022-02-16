<?php

function file_create_url_example() {
    $uri = 'public://foo.png';
    file_create_url($uri);
}

function file_url_transform_relative_example() {
    $uri = 'public://foo.png';
    file_url_transform_relative($uri);
}

function combined_example() {
    $uri = 'public://foo.png';
    file_url_transform_relative(file_create_url($uri));
}

function from_uri_example() {
    $uri = 'public://foo.png';
    \Drupal\Core\Url::fromUri(file_create_url($uri));
}
