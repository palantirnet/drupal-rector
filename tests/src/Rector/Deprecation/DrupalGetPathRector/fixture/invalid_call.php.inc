<?php

function simple_example() {
    drupal_get_path('module', 'node', 'baz', 'bar');
}
?>
-----
<?php

function simple_example() {
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // Invalid call to drupal_get_path, cannot process.
    drupal_get_path('module', 'node', 'baz', 'bar');
}
?>
