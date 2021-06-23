<?php

function simple_example() {
    $timezone = DATETIME_STORAGE_TIMEZONE;
}

function as_an_argument() {
    $timezone = new \DateTimeZone(DATETIME_STORAGE_TIMEZONE);
}
?>
-----
<?php

function simple_example() {
    $timezone = \Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface::STORAGE_TIMEZONE;
}

function as_an_argument() {
    $timezone = new \DateTimeZone(\Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface::STORAGE_TIMEZONE);
}
?>
