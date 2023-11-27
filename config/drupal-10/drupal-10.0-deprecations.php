<?php

declare(strict_types=1);

use DrupalRector\Rector\PHPUnit\ShouldCallParentMethodsRector;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\Symfony\Set\TwigLevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        PHPUnitLevelSetList::UP_TO_PHPUNIT_90,
        SymfonyLevelSetList::UP_TO_SYMFONY_62,
        TwigLevelSetList::UP_TO_TWIG_240,
    ]);

    $rectorConfig->rule(ShouldCallParentMethodsRector::class);
};
