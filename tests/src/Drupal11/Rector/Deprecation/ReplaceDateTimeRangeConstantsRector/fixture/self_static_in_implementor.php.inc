<?php

use Drupal\datetime_range\DateTimeRangeConstantsInterface;

class MyDateRangePlugin implements DateTimeRangeConstantsInterface {
    public function getDisplayType(): string {
        return self::BOTH;
    }

    public function getStartType(): string {
        return static::START_DATE;
    }
}

?>
-----
<?php

use Drupal\datetime_range\DateTimeRangeConstantsInterface;

class MyDateRangePlugin implements DateTimeRangeConstantsInterface {
    public function getDisplayType(): string {
        return self::BOTH;
    }

    public function getStartType(): string {
        return static::START_DATE;
    }
}

?>
