<?php

use Drupal\Core\Url;

function simple_example() {
    Url::fromUri('public://');
    $uri = 'public://foo.png';
    Url::fromUri(file_create_url($uri));
}
?>
-----
<?php

use Drupal\Core\Url;

function simple_example() {
    Url::fromUri('public://');
    $uri = 'public://foo.png';
    \Drupal::service('file_url_generator')->generate($uri);
}
?>
