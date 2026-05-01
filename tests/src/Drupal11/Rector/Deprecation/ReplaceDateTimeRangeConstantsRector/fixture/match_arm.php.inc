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
    \Drupal\datetime_range\DateTimeRangeDisplayOptions::Both->value => 'Both dates',
    \Drupal\datetime_range\DateTimeRangeDisplayOptions::StartDate->value => 'Start date only',
    \Drupal\datetime_range\DateTimeRangeDisplayOptions::EndDate->value => 'End date only',
};

?>
