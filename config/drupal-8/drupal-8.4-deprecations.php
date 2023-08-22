<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\GetMockRector;
use DrupalRector\Rector\ValueObject\GetMockConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/2907725
    $rectorConfig->ruleWithConfiguration(GetMockRector::class, [
        new GetMockConfiguration('Drupal\Tests\BrowserTestBase'),
        new GetMockConfiguration('Drupal\KernelTests\KernelTestBase'),
        new GetMockConfiguration('Drupal\Tests\UnitTestCase'),
    ]);
};
