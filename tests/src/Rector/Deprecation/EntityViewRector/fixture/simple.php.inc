<?php
function simple_example() {
    $entity = new stdClass();
    $view = entity_view($entity, 'default');
}
?>
-----
<?php
function simple_example() {
    $entity = new stdClass();
    $view = \Drupal::entityTypeManager()->getViewBuilder($entity->getEntityTypeId())->view($entity, 'default');
}
?>
