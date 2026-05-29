<?php

function example($batch_size, $field_storage_unique_id) {
    field_purge_batch($batch_size, $field_storage_unique_id);
}
?>
-----
<?php

function example($batch_size, $field_storage_unique_id) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\Core\Field\FieldPurger')->purgeBatch($batch_size, $field_storage_unique_id), fn() => field_purge_batch($batch_size, $field_storage_unique_id));
}
?>
