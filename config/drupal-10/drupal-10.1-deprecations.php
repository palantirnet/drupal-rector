<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\Deprecation\SystemTimeZonesRector;
use DrupalRector\Drupal10\Rector\Deprecation\WatchdogExceptionRector;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\PHPUnit100\Rector\Class_\PublicDataProviderClassMethodRector;
use Rector\PHPUnit\PHPUnit100\Rector\Class_\StaticDataProviderClassMethodRector;
use Rector\PHPUnit\PHPUnit100\Rector\MethodCall\RemoveSetMethodsMethodCallRector;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        SymfonySetList::SYMFONY_63,
    ]);

    // PHPUnit 10.0 rules
    $rectorConfig->rules([
        PublicDataProviderClassMethodRector::class,
        StaticDataProviderClassMethodRector::class,
        RemoveSetMethodsMethodCallRector::class,
    ]);

    // https://www.drupal.org/node/3244583
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('10.1.0', 'drupal_rewrite_settings', 'Drupal\Core\Site\SettingsEditor', 'rewrite', [0 => 1, 1 => 0]),
    ]);

    // https://www.drupal.org/node/2932520
    $rectorConfig->ruleWithConfiguration(WatchdogExceptionRector::class, [
        new DrupalIntroducedVersionConfiguration('10.1.0'),
    ]);

    // https://www.drupal.org/node/3023528
    $rectorConfig->ruleWithConfiguration(SystemTimeZonesRector::class, [
        new DrupalIntroducedVersionConfiguration('10.1.0'),
    ]);
};
