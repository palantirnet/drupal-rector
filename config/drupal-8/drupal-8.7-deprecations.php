<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\FilePrepareDirectoryRector;
use DrupalRector\Rector\Deprecation\FileUnmanagedSaveDataRector;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->rule(FilePrepareDirectoryRector::class);


    /**
     * Replaces deprecated FILE_CREATE_DIRECTORY constant use.
     *
     * No change record found.
     */
    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        ConstantToClassConstantRector::DEPRECATED_CONSTANT => 'FILE_CREATE_DIRECTORY',
        ConstantToClassConstantRector::CONSTANT_FULLY_QUALIFIED_CLASS_NAME => 'Drupal\Core\File\FileSystemInterface',
        ConstantToClassConstantRector::CONSTANT => 'CREATE_DIRECTORY',
    ]);

    /**
     * Replaces deprecated FILE_EXISTS_REPLACE constant use.
     *
     * See https://www.drupal.org/node/3006851 for change record.
     */
    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        ConstantToClassConstantRector::DEPRECATED_CONSTANT => 'FILE_EXISTS_REPLACE',
        ConstantToClassConstantRector::CONSTANT_FULLY_QUALIFIED_CLASS_NAME => 'Drupal\Core\File\FileSystemInterface',
        ConstantToClassConstantRector::CONSTANT => 'EXISTS_REPLACE',
    ]);

    /**
     * Replaces deprecated FILE_EXISTS_RENAME constant use.
     *
     * See https://www.drupal.org/node/3006851 for change record.
     */
    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        ConstantToClassConstantRector::DEPRECATED_CONSTANT => 'FILE_EXISTS_RENAME',
        ConstantToClassConstantRector::CONSTANT_FULLY_QUALIFIED_CLASS_NAME => 'Drupal\Core\File\FileSystemInterface',
        ConstantToClassConstantRector::CONSTANT => 'EXISTS_RENAME',
    ]);

    /**
     * Replaces deprecated FILE_MODIFY_PERMISSIONS constant use.
     *
     * No change record found.
     */
    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        ConstantToClassConstantRector::DEPRECATED_CONSTANT => 'FILE_MODIFY_PERMISSIONS',
        ConstantToClassConstantRector::CONSTANT_FULLY_QUALIFIED_CLASS_NAME => 'Drupal\Core\File\FileSystemInterface',
        ConstantToClassConstantRector::CONSTANT => 'MODIFY_PERMISSIONS',
    ]);

    $rectorConfig->rule(FileUnmanagedSaveDataRector::class);
};
