<?php

function simple_example() {
    drupal_get_path('library', 'node');
}
?>
-----
<?php

function simple_example() {
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // Unsupported extension type encountered, using extension.path.resolver instead of extension.list
    \Drupal::service('extension.path.resolver')->getPath('library', 'node');
}
?>
