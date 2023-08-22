<?php

declare(strict_types=1);

use DrupalRector\Rector\ValueObject\StaticToFunctionConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(\DrupalRector\Rector\Deprecation\StaticToFunctionRector::class, [
        // https://www.drupal.org/node/2850048
        new StaticToFunctionConfiguration('Drupal\Component\Utility\Unicode', 'strlen', 'mb_strlen'),
        // https://www.drupal.org/node/2850048
        new StaticToFunctionConfiguration('Drupal\Component\Utility\Unicode', 'strtolower', 'mb_strtolower'),
        // https://www.drupal.org/node/2850048
        new StaticToFunctionConfiguration('Drupal\Component\Utility\Unicode', 'substr', 'mb_substr'),
    ]);
};
