<?php

datetime_type_field_views_data_helper($field_storage, $data, 'value');

$result = datetime_type_field_views_data_helper($field_storage, $data, $column);

?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::service('datetime.views_helper')->buildViewsData($field_storage, $data, 'value'), fn() => datetime_type_field_views_data_helper($field_storage, $data, 'value'));

$result = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::service('datetime.views_helper')->buildViewsData($field_storage, $data, $column), fn() => datetime_type_field_views_data_helper($field_storage, $data, $column));

?>
