<?php

declare(strict_types=1);

use DrupalRector\Rector\Property\ProtectedStaticModulesPropertyRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Set\TwigSetList;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        PHPUnitSetList::PHPUNIT_80,
        SymfonySetList::SYMFONY_40,
        SymfonySetList::SYMFONY_41,
        SymfonySetList::SYMFONY_42,
        SymfonySetList::SYMFONY_43,
        SymfonySetList::SYMFONY_44,
        TwigSetList::TWIG_240
    ]);
    $rectorConfig->rule(ProtectedStaticModulesPropertyRector::class);
};
