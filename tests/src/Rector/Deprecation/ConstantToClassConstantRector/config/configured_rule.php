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
    DeprecationBase::addClass(ConstantToClassConstantRector::class, $rectorConfig, false, [
        new ConstantToClassConfiguration('REQUIREMENT_INFO',    'Drupal\Core\Extension\Requirement\RequirementSeverity', 'Info'),
        new ConstantToClassConfiguration('REQUIREMENT_OK',      'Drupal\Core\Extension\Requirement\RequirementSeverity', 'OK'),
        new ConstantToClassConfiguration('REQUIREMENT_WARNING', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'Warning'),
        new ConstantToClassConfiguration('REQUIREMENT_ERROR',   'Drupal\Core\Extension\Requirement\RequirementSeverity', 'Error'),
        new ConstantToClassConfiguration('LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN', 'Drupal', 'TRANSLATION_DEFAULT_SERVER_PATTERN'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_ALL',       'Drupal\jsonapi\JsonApiFilter', 'AMONG_ALL'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_PUBLISHED', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_PUBLISHED'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_ENABLED',   'Drupal\jsonapi\JsonApiFilter', 'AMONG_ENABLED'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_OWN',       'Drupal\jsonapi\JsonApiFilter', 'AMONG_OWN'),
    ]);
};
