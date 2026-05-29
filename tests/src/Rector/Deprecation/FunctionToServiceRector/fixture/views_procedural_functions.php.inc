<?php

function example($field_storage, $entity_type, $field_name) {
    $storage = _views_field_get_entity_type_storage($field_storage);
    $labels = views_entity_field_label($entity_type, $field_name);
    $data = views_field_default_views_data($field_storage);
}
?>
-----
<?php

function example($field_storage, $entity_type, $field_name) {
    $storage = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::service('views.field_data_provider')->getSqlStorageForField($field_storage), fn() => _views_field_get_entity_type_storage($field_storage));
    $labels = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::service('entity_field.manager')->getFieldLabels($entity_type, $field_name), fn() => views_entity_field_label($entity_type, $field_name));
    $data = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::service('views.field_data_provider')->defaultFieldImplementation($field_storage), fn() => views_field_default_views_data($field_storage));
}
?>
