<?php

declare(strict_types=1);

use DrupalRector\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector;
use DrupalRector\Rector\PHPUnit\ValueObject\PhpUnitTestAnnotationToAttributeConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(PhpUnitTestAnnotationToAttributeRector::class, $rectorConfig, false, [
        new PhpUnitTestAnnotationToAttributeConfiguration('11.0.0', '12.0.0', 'group', 'PHPUnit\Framework\Attributes\Group'),
        new PhpUnitTestAnnotationToAttributeConfiguration('11.0.0', '12.0.0', 'dataProvider', 'PHPUnit\Framework\Attributes\DataProvider'),
    ]);

    // Backward-compatibility is disabled per-test in BackwardCompatibilityDisabledPhpUnitTestAnnotationToAttributeRectorTest::setUp(),
    // because AbstractDrupalRectorTestCase::tearDown() re-enables it after every test.
};
