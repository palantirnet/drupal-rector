<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\RemoveStateCacheSettingRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3436954
    // $settings['state_cache'] deprecated in drupal:11.0.0.
    // State caching is now permanently enabled and the setting has no effect.
    $rectorConfig->rule(RemoveStateCacheSettingRector::class);
};
