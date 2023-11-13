<?php

function simple_example() {
    $uri = 'public://foo.png';
    file_create_url($uri);
}
?>
-----
<?php

function simple_example() {
    $uri = 'public://foo.png';
    \Drupal::service('file_url_generator')->generateAbsoluteString($uri);
}
?>
