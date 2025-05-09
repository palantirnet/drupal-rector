<?php

declare(strict_types=1);

use DrupalRector\Rector\PHPUnit\ShouldCallParentMethodsRector;
use Rector\Config\RectorConfig;
use Rector\Exception\ShouldNotHappenException;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Set\TwigSetList;

return static function (RectorConfig $rectorConfig): void {
    if (defined(TwigSetList::class.'::TWIG_24')) {
        $twigSet = TwigSetList::TWIG_24;
    } elseif (defined(TwigSetList::class.'::TWIG_240')) {
        $twigSet = TwigSetList::TWIG_240;
    } else {
        throw new ShouldNotHappenException('Could not detect twig set.');
    }

    $rectorConfig->sets([
        PHPUnitSetList::PHPUNIT_90,
        SymfonySetList::SYMFONY_50,
        SymfonySetList::SYMFONY_51,
        SymfonySetList::SYMFONY_52,
        SymfonySetList::SYMFONY_53,
        SymfonySetList::SYMFONY_54,
        SymfonySetList::SYMFONY_60,
        SymfonySetList::SYMFONY_61,
        SymfonySetList::SYMFONY_62,
        $twigSet,
    ]);

    $rectorConfig->rule(ShouldCallParentMethodsRector::class);
};
