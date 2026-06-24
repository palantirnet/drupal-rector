<?php

declare(strict_types=1);

namespace Drupal\Core\Datetime;

if (class_exists(\Drupal\Core\Datetime\DatePreprocess::class)) {
    return;
}

class DatePreprocess
{
    public function preprocessTime(&$variables): void {}

    public function preprocessDatetimeForm(&$variables): void {}

    public function preprocessDatetimeWrapper(&$variables): void {}
}
