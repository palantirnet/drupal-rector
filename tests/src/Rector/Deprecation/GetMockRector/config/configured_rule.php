<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\GetMockRector;
use DrupalRector\Rector\ValueObject\GetMockConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(GetMockRector::class, $rectorConfig, FALSE, [
        new GetMockConfiguration('Drupal\Tests\BrowserTestBase'),
        new GetMockConfiguration('Drupal\KernelTests\KernelTestBase'),
        new GetMockConfiguration('Drupal\Tests\UnitTestCase'),
    ]);
};
