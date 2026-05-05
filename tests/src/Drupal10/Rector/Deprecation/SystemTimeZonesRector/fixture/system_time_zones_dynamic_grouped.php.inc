<?php

// Second arg is a variable — must not silently drop it.
function dynamic_grouped_example() {
    $grouped = TRUE;
    system_time_zones(FALSE, $grouped);
}

?>
-----
<?php

// Second arg is a variable — must not silently drop it.
function dynamic_grouped_example() {
    $grouped = TRUE;
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => $grouped ? \Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsListByRegion(FALSE) : \Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsList(FALSE), fn() => system_time_zones(FALSE, $grouped));
}

?>
