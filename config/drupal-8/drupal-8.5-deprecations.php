<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\DrupalSetMessageRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(DrupalSetMessageRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
    ]);


    /**
     * Replaces deprecated DATETIME_DATE_STORAGE_FORMAT constant use.
     *
     * See https://www.drupal.org/node/2912980 for change record.
     */
    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        ConstantToClassConstantRector::DEPRECATED_CONSTANT => 'DATETIME_DATE_STORAGE_FORMAT',
        ConstantToClassConstantRector::CONSTANT_FULLY_QUALIFIED_CLASS_NAME => 'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface',
        ConstantToClassConstantRector::CONSTANT => 'DATE_STORAGE_FORMAT',
    ]);

    /**
     * Replaces deprecated DATETIME_DATETIME_STORAGE_FORMAT constant use.
     *
     * See https://www.drupal.org/node/2912980 for change record.
     */
    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        ConstantToClassConstantRector::DEPRECATED_CONSTANT => 'DATETIME_DATETIME_STORAGE_FORMAT',
        ConstantToClassConstantRector::CONSTANT_FULLY_QUALIFIED_CLASS_NAME => 'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface',
        ConstantToClassConstantRector::CONSTANT => 'DATETIME_STORAGE_FORMAT',
    ]);

    /**
     * Replaces deprecated DATETIME_STORAGE_TIMEZONE constant use.
     *
     * See https://www.drupal.org/node/2912980 for change record.
     */
    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        ConstantToClassConstantRector::DEPRECATED_CONSTANT => 'DATETIME_STORAGE_TIMEZONE',
        ConstantToClassConstantRector::CONSTANT_FULLY_QUALIFIED_CLASS_NAME => 'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface',
        ConstantToClassConstantRector::CONSTANT => 'STORAGE_TIMEZONE',
    ]);
};
