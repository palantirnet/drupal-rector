<?php

declare(strict_types=1);

use DrupalRector\Drupal9\Rector\Property\ProtectedStaticModulesPropertyRector;
use DrupalRector\Rector\PHPUnit\ShouldCallParentMethodsRector;
use DrupalRector\Services\AddCommentService;
use Rector\Config\RectorConfig;
use Rector\Exception\ShouldNotHappenException;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Set\TwigSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, function () {
        return new AddCommentService();
    });

    if (defined(TwigSetList::class.'::TWIG_24')) {
        $twigSet = TwigSetList::TWIG_24;
    } elseif (defined(TwigSetList::class.'::TWIG_240')) {
        $twigSet = TwigSetList::TWIG_240;
    } else {
        throw new ShouldNotHappenException('Could not detect twig set.');
    }

    $rectorConfig->sets([
        PHPUnitSetList::PHPUNIT_80,
        SymfonySetList::SYMFONY_40,
        SymfonySetList::SYMFONY_41,
        SymfonySetList::SYMFONY_42,
        SymfonySetList::SYMFONY_43,
        SymfonySetList::SYMFONY_44,
        $twigSet,
    ]);
    $rectorConfig->rule(ProtectedStaticModulesPropertyRector::class);

    $rectorConfig->rule(ShouldCallParentMethodsRector::class);
};
