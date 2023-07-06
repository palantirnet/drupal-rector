<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ConstantToClassConstantRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(ConstantToClassConstantRector::class, $rectorConfig, false, [
        ConstantToClassConstantRector::DEPRECATED_CONSTANT => 'DATETIME_STORAGE_TIMEZONE',
        ConstantToClassConstantRector::CONSTANT_FULLY_QUALIFIED_CLASS_NAME => 'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface',
        ConstantToClassConstantRector::CONSTANT => 'STORAGE_TIMEZONE',
    ]);
};
