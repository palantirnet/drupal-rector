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

$a = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\datetime_range\DateTimeRangeDisplayOptions::Both->value, fn() => DateTimeRangeConstantsInterface::BOTH);
$b = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\datetime_range\DateTimeRangeDisplayOptions::StartDate->value, fn() => DateTimeRangeConstantsInterface::START_DATE);
$c = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\datetime_range\DateTimeRangeDisplayOptions::EndDate->value, fn() => DateTimeRangeConstantsInterface::END_DATE);
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::service('datetime.views_helper')->buildViewsData($field_storage, $data, 'value'), fn() => datetime_type_field_views_data_helper($field_storage, $data, 'value'));

?>
