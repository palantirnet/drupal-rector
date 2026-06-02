<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\RemoveToolkitArgFromImageToolkitOperationConstructorRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(RemoveToolkitArgFromImageToolkitOperationConstructorRector::class);
};
