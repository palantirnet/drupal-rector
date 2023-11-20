<?php

declare(strict_types=1);

use DrupalRector\Drupal8\Rector\Deprecation\GetMockRector;
use DrupalRector\Drupal8\Rector\ValueObject\GetMockConfiguration;
use DrupalRector\Services\AddCommentService;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, function () {
        return new AddCommentService();
    });
    // https://www.drupal.org/node/2907725
    $rectorConfig->ruleWithConfiguration(GetMockRector::class, [
        new GetMockConfiguration('Drupal\Tests\BrowserTestBase'),
        new GetMockConfiguration('Drupal\KernelTests\KernelTestBase'),
        new GetMockConfiguration('Drupal\Tests\UnitTestCase'),
    ]);
};
