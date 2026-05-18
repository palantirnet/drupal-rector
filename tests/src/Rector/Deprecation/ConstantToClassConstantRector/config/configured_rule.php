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
            '8.5.0',
        ),
    ]);
    DeprecationBase::addClass(ConstantToClassConstantRector::class, $rectorConfig, false, [
        new ConstantToClassConfiguration(
            'FILE_STATUS_PERMANENT',
            'Drupal\file\FileInterface',
            'STATUS_PERMANENT',
            '9.3.0',
        ),
    ]);
    DeprecationBase::addClass(ConstantToClassConstantRector::class, $rectorConfig, false, [
        new ConstantToClassConfiguration('REQUIREMENT_INFO', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'Info', '11.2.0'),
        new ConstantToClassConfiguration('REQUIREMENT_OK', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'OK', '11.2.0'),
        new ConstantToClassConfiguration('REQUIREMENT_WARNING', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'Warning', '11.2.0'),
        new ConstantToClassConfiguration('REQUIREMENT_ERROR', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'Error', '11.2.0'),
        new ConstantToClassConfiguration('LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN', 'Drupal', 'TRANSLATION_DEFAULT_SERVER_PATTERN', '11.2.0'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_ALL', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_ALL', '11.3.0'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_PUBLISHED', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_PUBLISHED', '11.3.0'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_ENABLED', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_ENABLED', '11.3.0'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_OWN', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_OWN', '11.3.0'),
    ]);
};
