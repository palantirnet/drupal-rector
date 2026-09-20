<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\RemoveTrustDataCallRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RemoveTrustDataCallRector::class, [], 'drupal/core', '>=11.4.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(
        RemoveTrustDataCallRector::class,
        [],
        'drupal/coder',
        '>=11.4.0'
    );

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RemoveTrustDataCallRector::class, [], 'drupal/core', '>=11.3');
};
