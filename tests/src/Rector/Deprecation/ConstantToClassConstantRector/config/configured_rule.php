<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ConstantToClassConstantRector;
use DrupalRector\Rector\ValueObject\ConstantToClassConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(ConstantToClassConstantRector::class, $rectorConfig, false, [
        new ConstantToClassConfiguration(
            'DATETIME_STORAGE_TIMEZONE',
            'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface',
            'STORAGE_TIMEZONE',
        ),
    ]);
    DeprecationBase::addClass(ConstantToClassConstantRector::class, $rectorConfig, false, [
        new ConstantToClassConfiguration(
            'FILE_STATUS_PERMANENT',
            'Drupal\file\FileInterface',
            'STATUS_PERMANENT',
        ),
    ]);
};
