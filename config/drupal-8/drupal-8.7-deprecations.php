<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\FilePrepareDirectoryRector;
use DrupalRector\Rector\Deprecation\FileUnmanagedSaveDataRector;
use DrupalRector\Rector\ValueObject\ConstantToClass;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->rule(FilePrepareDirectoryRector::class);


    /**
     * Replaces deprecated FILE_CREATE_DIRECTORY constant use.
     *
     * No change record found.
     */
    $constantToClassFileCreateDirectory = new ConstantToClass('FILE_CREATE_DIRECTORY', 'Drupal\Core\File\FileSystemInterface', 'CREATE_DIRECTORY');

    /**
     * Replaces deprecated FILE_EXISTS_REPLACE, FILE_EXISTS_RENAME constant use.
     *
     * See https://www.drupal.org/node/3006851 for change record.
     */
    $constantToClassFileExistReplace = new ConstantToClass('FILE_EXISTS_REPLACE', 'Drupal\Core\File\FileSystemInterface', 'EXISTS_REPLACE');
    $constantToClassFileExistsRename = new ConstantToClass('FILE_EXISTS_RENAME', 'Drupal\Core\File\FileSystemInterface', 'EXISTS_RENAME');

    /**
     * Replaces deprecated FILE_MODIFY_PERMISSIONS constant use.
     *
     * No change record found.
     */
    $constantToClassFileModifyPermissions = new ConstantToClass('FILE_MODIFY_PERMISSIONS', 'Drupal\Core\File\FileSystemInterface', 'MODIFY_PERMISSIONS');

    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        $constantToClassFileCreateDirectory,
        $constantToClassFileExistReplace,
        $constantToClassFileExistsRename,
        $constantToClassFileModifyPermissions,
    ]);

    $rectorConfig->rule(FileUnmanagedSaveDataRector::class);
};
