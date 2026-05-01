<?php

datetime_type_field_views_data_helper($field_storage, $data, 'value');

$result = datetime_type_field_views_data_helper($field_storage, $data, $column);

?>
-----
<?php

\Drupal::service('datetime.views_helper')->buildViewsData($field_storage, $data, 'value');

$result = \Drupal::service('datetime.views_helper')->buildViewsData($field_storage, $data, $column);

?>
