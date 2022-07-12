<?php
function lagncode_example() {
    $entity = new stdClass();
    $langcode = 'de';
    $view = entity_view($entity, 'default', $langcode);
}
?>
-----
<?php
function lagncode_example() {
    $entity = new stdClass();
    $langcode = 'de';
    $view = \Drupal::entityTypeManager()->getViewBuilder($entity->getEntityTypeId())->view($entity, 'default', $langcode);
}
?>
