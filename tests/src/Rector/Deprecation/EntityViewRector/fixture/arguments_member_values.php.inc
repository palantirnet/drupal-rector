<?php
function arguments_member_values() {
    $entity = new stdClass();
    $entity->field_view_mode = 'test';

    $view = entity_view($entity, $entity->field_view_mode);
}
?>
-----
<?php
function arguments_member_values() {
    $entity = new stdClass();
    $entity->field_view_mode = 'test';

    $view = \Drupal::entityTypeManager()->getViewBuilder($entity->getEntityTypeId())->view($entity, $entity->field_view_mode);
}
?>
