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
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => system_time_zones(), fn() => \Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsList());

    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => system_time_zones(FALSE, TRUE), fn() => \Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsListByRegion());

    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => system_time_zones(NULL, FALSE), fn() => \Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsList(NULL));

    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => system_time_zones(TRUE, FALSE), fn() => \Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsList(TRUE));
}
?>
