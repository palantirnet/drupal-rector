<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\ValueObject\ConstantToClassConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Services\AddCommentService;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, function () {
        return new AddCommentService();
    });
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        // https://www.drupal.org/node/3006851
        new FunctionToServiceConfiguration('8.7.0', 'file_prepare_directory', 'file_system', 'prepareDirectory'),
        // https://www.drupal.org/node/3006851
        new FunctionToServiceConfiguration('8.7.0', 'file_unmanaged_save_data', 'file_system', 'saveData'),
    ]);

    /**
     * Replaces deprecated FILE_CREATE_DIRECTORY constant use.
     *
     * No change record found.
     */
    $constantToClassFileCreateDirectory = new ConstantToClassConfiguration('FILE_CREATE_DIRECTORY', 'Drupal\Core\File\FileSystemInterface', 'CREATE_DIRECTORY');

    /**
     * Replaces deprecated FILE_EXISTS_REPLACE, FILE_EXISTS_RENAME constant use.
     *
     * See https://www.drupal.org/node/3006851 for change record.
     */
    $constantToClassFileExistReplace = new ConstantToClassConfiguration('FILE_EXISTS_REPLACE', 'Drupal\Core\File\FileSystemInterface', 'EXISTS_REPLACE');
    $constantToClassFileExistsRename = new ConstantToClassConfiguration('FILE_EXISTS_RENAME', 'Drupal\Core\File\FileSystemInterface', 'EXISTS_RENAME');

    /**
     * Replaces deprecated FILE_MODIFY_PERMISSIONS constant use.
     *
     * No change record found.
     */
    $constantToClassFileModifyPermissions = new ConstantToClassConfiguration('FILE_MODIFY_PERMISSIONS', 'Drupal\Core\File\FileSystemInterface', 'MODIFY_PERMISSIONS');

    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        $constantToClassFileCreateDirectory,
        $constantToClassFileExistReplace,
        $constantToClassFileExistsRename,
        $constantToClassFileModifyPermissions,
    ]);
};
