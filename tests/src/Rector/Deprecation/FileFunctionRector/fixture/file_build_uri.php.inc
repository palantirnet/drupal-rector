<?php

function file_functions()
{
    $uri1 = file_build_uri('path/to/file.txt');
    $path = 'path/to/other/file.png';
    $uri2 = file_build_uri($path);
    $dir = 'path/to';
    $uri3 = file_build_uri("$dir/file.jpg");
    $uri4 = file_build_uri($dir . '/' . 'file1.jpg');
}
?>
-----
<?php

function file_functions()
{
    $uri1 = \Drupal::service('stream_wrapper_manager')->normalizeUri(\Drupal::config('system.file')->get('default_scheme') . ('://' . 'path/to/file.txt'));
    $path = 'path/to/other/file.png';
    $uri2 = \Drupal::service('stream_wrapper_manager')->normalizeUri(\Drupal::config('system.file')->get('default_scheme') . ('://' . $path));
    $dir = 'path/to';
    $uri3 = \Drupal::service('stream_wrapper_manager')->normalizeUri(\Drupal::config('system.file')->get('default_scheme') . ('://' . "$dir/file.jpg"));
    $uri4 = \Drupal::service('stream_wrapper_manager')->normalizeUri(\Drupal::config('system.file')->get('default_scheme') . ('://' . ($dir . '/' . 'file1.jpg')));
}
?>
