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

    // Naming the major the deprecation is removed in is valid.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RemoveTrustDataCallRector::class, [], 'drupal/core', '>=11.3.0 <13.0.0');

    // Drupal removes deprecated API only on a major boundary.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RemoveTrustDataCallRector::class, [], 'drupal/core', '>=11.3.0 <12.1.0');

    // The upper bound has to be a later major than the lower bound.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RemoveTrustDataCallRector::class, [], 'drupal/core', '>=11.3.0 <11.0.0');

    // A rule may follow another package, and an API that predates the rule has no lower bound.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RemoveTrustDataCallRector::class, [], 'phpunit/phpunit', '<6.0.0');

    // A core deprecation always has a version it was introduced in.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RemoveTrustDataCallRector::class, [], 'drupal/core', '<12.0.0');
};
