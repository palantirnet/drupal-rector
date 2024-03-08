<?php

declare(strict_types=1);

use DrupalRector\Drupal8\Rector\Deprecation\DrupalSetMessageRector;
use DrupalRector\Rector\Deprecation\ConstantToClassConstantRector;
use DrupalRector\Rector\ValueObject\ConstantToClassConfiguration;
use DrupalRector\Services\AddCommentService;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, function () {
        return new AddCommentService();
    });
    $rectorConfig->rule(DrupalSetMessageRector::class);

    /*
     * Replaces deprecated DATETIME_DATE_STORAGE_FORMAT, DATETIME_DATETIME_STORAGE_FORMAT, DATETIME_STORAGE_TIMEZONE constant use.
     *
     * See https://www.drupal.org/node/2912980 for change record.
     */
    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        new ConstantToClassConfiguration('DATETIME_DATE_STORAGE_FORMAT', 'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface', 'DATE_STORAGE_FORMAT'),
        new ConstantToClassConfiguration('DATETIME_DATETIME_STORAGE_FORMAT', 'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface', 'DATETIME_STORAGE_FORMAT'),
        new ConstantToClassConfiguration('DATETIME_STORAGE_TIMEZONE', 'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface', 'STORAGE_TIMEZONE'),
    ]);
};
