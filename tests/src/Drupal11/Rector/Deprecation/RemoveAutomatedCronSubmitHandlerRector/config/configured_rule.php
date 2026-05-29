<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\RemoveAutomatedCronSubmitHandlerRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(RemoveAutomatedCronSubmitHandlerRector::class);
};
