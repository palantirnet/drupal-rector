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
    $grouped ? \Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsListByRegion(FALSE) : \Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsList(FALSE);
}

?>
