<?php

function reset_file_reference_statics() {
    drupal_static_reset('file_get_file_references');
    drupal_static_reset('file_get_file_references:field_columns');
}

?>
-----
<?php

function reset_file_reference_statics() {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('cache.memory')->invalidateTags(['file_references']), fn() => drupal_static_reset('file_get_file_references'));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('cache.memory')->invalidateTags(['file_references']), fn() => drupal_static_reset('file_get_file_references:field_columns'));
}

?>
