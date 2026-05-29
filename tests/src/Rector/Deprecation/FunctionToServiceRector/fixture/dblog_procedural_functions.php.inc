<?php

function example() {
    $types = _dblog_get_message_types();
    $filters = dblog_filters();
}
?>
-----
<?php

function example() {
    $types = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\dblog\DbLogFilters')->getMessageTypes(), fn() => _dblog_get_message_types());
    $filters = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\dblog\DbLogFilters')->filters(), fn() => dblog_filters());
}
?>
