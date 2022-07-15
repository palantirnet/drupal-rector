<?php
function simple_example() {
    entity_delete_multiple('node', [1, 2, 3]);
}
?>
-----
<?php
function simple_example() {
    \Drupal::service('entity_type.manager')->getStorage('node')->delete(\Drupal::service('entity_type.manager')->getStorage('node')->loadMultiple([1, 2, 3]));
}
?>
