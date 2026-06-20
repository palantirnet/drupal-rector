<?php

declare(strict_types=1);

use DrupalRector\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector;
use DrupalRector\Rector\PHPUnit\ValueObject\PhpUnitTestAnnotationToAttributeConfiguration;
use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(PhpUnitTestAnnotationToAttributeRector::class, $rectorConfig, false, [
        new PhpUnitTestAnnotationToAttributeConfiguration('11.0.0', '12.0.0', 'group', 'PHPUnit\Framework\Attributes\Group'),
        new PhpUnitTestAnnotationToAttributeConfiguration('11.0.0', '12.0.0', 'dataProvider', 'PHPUnit\Framework\Attributes\DataProvider'),
    ]);

    // Disable BC on the shared singleton so annotations are removed.
    // Note: setUp() in the test class re-applies this after each tearDown() reset.
    $rectorConfig->make(DrupalRectorSettings::class)->disableBackwardCompatibility();
};
