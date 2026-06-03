<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\ReplaceExpectDeprecationRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(ReplaceExpectDeprecationRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ]);
};
