<?php

declare(strict_types=1);

use DrupalRector\Drupal9\Rector\Property\ProtectedStaticModulesPropertyRector;
use DrupalRector\Rector\PHPUnit\ShouldCallParentMethodsRector;
use DrupalRector\Services\AddCommentService;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, function () {
        return new AddCommentService();
    });

    $rectorConfig->rule(ProtectedStaticModulesPropertyRector::class);

    $rectorConfig->rule(ShouldCallParentMethodsRector::class);
};
