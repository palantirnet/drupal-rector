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
        new PhpUnitTestAnnotationToAttributeConfiguration('11.0.0', '12.0.0', 'depends', 'PHPUnit\Framework\Attributes\Depends'),
    ]);
};
