<?php

function simple_example() {
    $build = [];
    $output = render($build);
}
?>
-----
<?php

function simple_example() {
    $build = [];
    $output = \Drupal::service('renderer')->render($build);
}
?>
