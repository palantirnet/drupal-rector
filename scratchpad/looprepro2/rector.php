<?php
declare(strict_types=1);
use DrupalRector\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector;
use DrupalRector\Rector\PHPUnit\ValueObject\PhpUnitTestAnnotationToAttributeConfiguration;
use DrupalRector\Services\DrupalRectorSettings;
use Rector\Config\RectorConfig;
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(DrupalRectorSettings::class, fn () => (new DrupalRectorSettings())->setMinimumCoreVersionSupported('10.1.0'));
    $rectorConfig->ruleWithConfiguration(PhpUnitTestAnnotationToAttributeRector::class, [
        new PhpUnitTestAnnotationToAttributeConfiguration('11.0.0', '12.0.0', 'group', 'PHPUnit\Framework\Attributes\Group'),
    ]);
    $rectorConfig->importNames(true, false);
    $rectorConfig->importShortClasses(false);
};
