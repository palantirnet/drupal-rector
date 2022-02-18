<?php

function simple_example() {
    $uri = 'public://foo.png';
    file_url_transform_relative(file_create_url($uri));
}
?>
-----
<?php

function simple_example() {
    $uri = 'public://foo.png';
    \Drupal::service('file_url_generator')->generateString($uri);
}
?>
