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
        PHPUnitLevelSetList::UP_TO_PHPUNIT_90,
        SymfonyLevelSetList::UP_TO_SYMFONY_63,
        TwigLevelSetList::UP_TO_TWIG_240,
    ]);

    // https://www.drupal.org/node/3244583
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('drupal_rewrite_settings', 'Drupal\Core\Site\SettingsEditor', 'rewrite', [0 => 1, 1 => 0]),
    ]);
};
