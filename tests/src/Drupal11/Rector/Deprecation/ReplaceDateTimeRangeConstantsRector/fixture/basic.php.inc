<?php

use Drupal\datetime_range\DateTimeRangeConstantsInterface;

$a = DateTimeRangeConstantsInterface::BOTH;
$b = DateTimeRangeConstantsInterface::START_DATE;
$c = DateTimeRangeConstantsInterface::END_DATE;
datetime_type_field_views_data_helper($field_storage, $data, 'value');

?>
-----
<?php

use Drupal\datetime_range\DateTimeRangeConstantsInterface;

$a = \Drupal\datetime_range\DateTimeRangeDisplayOptions::Both->value;
$b = \Drupal\datetime_range\DateTimeRangeDisplayOptions::StartDate->value;
$c = \Drupal\datetime_range\DateTimeRangeDisplayOptions::EndDate->value;
\Drupal::service('datetime.views_helper')->buildViewsData($field_storage, $data, 'value');

?>
