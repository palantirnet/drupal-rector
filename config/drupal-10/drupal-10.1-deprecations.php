<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\Symfony\Set\TwigLevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        SymfonyLevelSetList::UP_TO_SYMFONY_63,
    ]);

    // https://www.drupal.org/node/3244583
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('10.1.0', 'drupal_rewrite_settings', 'Drupal\Core\Site\SettingsEditor', 'rewrite', [0 => 1, 1 => 0]),
    ]);
};
