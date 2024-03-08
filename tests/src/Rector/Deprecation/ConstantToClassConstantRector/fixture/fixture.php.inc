<?php

function simple_example() {
    $timezone = DATETIME_STORAGE_TIMEZONE;

    $file_status = FILE_STATUS_PERMANENT;
}

function as_an_argument() {
    $timezone = new \DateTimeZone(DATETIME_STORAGE_TIMEZONE);

    $class = new \TestClass(FILE_STATUS_PERMANENT);
}

class ClassDefault {
    public function test($status = FILE_STATUS_PERMANENT) {
        return true;
    }
}

?>
-----
<?php

function simple_example() {
    $timezone = \Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface::STORAGE_TIMEZONE;

    $file_status = \Drupal\file\FileInterface::STATUS_PERMANENT;
}

function as_an_argument() {
    $timezone = new \DateTimeZone(\Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface::STORAGE_TIMEZONE);

    $class = new \TestClass(\Drupal\file\FileInterface::STATUS_PERMANENT);
}

class ClassDefault {
    public function test($status = \Drupal\file\FileInterface::STATUS_PERMANENT) {
        return true;
    }
}

?>
