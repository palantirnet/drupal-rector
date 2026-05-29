<?php

use Drupal\datetime_range\DateTimeRangeConstantsInterface;

$type = 'both';

$label = match($type) {
    DateTimeRangeConstantsInterface::BOTH => 'Both dates',
    DateTimeRangeConstantsInterface::START_DATE => 'Start date only',
    DateTimeRangeConstantsInterface::END_DATE => 'End date only',
};

?>
-----
<?php

use Drupal\datetime_range\DateTimeRangeConstantsInterface;

$type = 'both';

$label = match($type) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\datetime_range\DateTimeRangeDisplayOptions::Both->value, fn() => DateTimeRangeConstantsInterface::BOTH) => 'Both dates',
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\datetime_range\DateTimeRangeDisplayOptions::StartDate->value, fn() => DateTimeRangeConstantsInterface::START_DATE) => 'Start date only',
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\datetime_range\DateTimeRangeDisplayOptions::EndDate->value, fn() => DateTimeRangeConstantsInterface::END_DATE) => 'End date only',
};

?>
