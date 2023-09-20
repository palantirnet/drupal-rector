<?php

declare(strict_types=1);

use DrupalRector\Rector\Property\ProtectedStaticModulesPropertyRector;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Set\TwigSetList;
use DrupalRector\Services\AddCommentService;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, function() {
        return new AddCommentService();
    });

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
