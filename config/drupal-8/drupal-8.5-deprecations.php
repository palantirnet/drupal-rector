<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\DrupalSetMessageRector;
use DrupalRector\Rector\ValueObject\ConstantToClass;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(DrupalSetMessageRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
    ]);


    /**
     * Replaces deprecated DATETIME_DATE_STORAGE_FORMAT, DATETIME_DATETIME_STORAGE_FORMAT, DATETIME_STORAGE_TIMEZONE constant use.
     *
     * See https://www.drupal.org/node/2912980 for change record.
     */
    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        new ConstantToClass('DATETIME_DATE_STORAGE_FORMAT', 'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface', 'DATE_STORAGE_FORMAT'),
        new ConstantToClass('DATETIME_DATETIME_STORAGE_FORMAT', 'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface', 'DATETIME_STORAGE_FORMAT'),
        new ConstantToClass('DATETIME_STORAGE_TIMEZONE', 'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface', 'STORAGE_TIMEZONE'),
    ]);
};
