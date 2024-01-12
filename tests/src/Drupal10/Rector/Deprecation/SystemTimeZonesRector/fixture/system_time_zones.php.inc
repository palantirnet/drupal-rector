<?php

function simple_example() {
    system_time_zones();

    system_time_zones(FALSE, TRUE);

    system_time_zones(NULL, FALSE);

    system_time_zones(TRUE, FALSE);
}
?>

-----
<?php

function simple_example() {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => \Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsList(), fn() => system_time_zones());

    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => \Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsListByRegion(), fn() => system_time_zones(FALSE, TRUE));

    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => \Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsList(NULL), fn() => system_time_zones(NULL, FALSE));

    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => \Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsList(TRUE), fn() => system_time_zones(TRUE, FALSE));
}
?>
