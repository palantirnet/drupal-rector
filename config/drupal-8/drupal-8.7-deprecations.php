<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FileCreateDirectoryRector;
use DrupalRector\Rector\Deprecation\FileExistsRenameRector;
use DrupalRector\Rector\Deprecation\FileExistsReplaceRector;
use DrupalRector\Rector\Deprecation\FileModifyPermissionsRector;
use DrupalRector\Rector\Deprecation\FilePrepareDirectoryRector;
use DrupalRector\Rector\Deprecation\FileUnmanagedSaveDataRector;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->rule(FilePrepareDirectoryRector::class);
    $rectorConfig->rule(FileCreateDirectoryRector::class);
    $rectorConfig->rule(FileExistsReplaceRector::class);
    $rectorConfig->rule(FileUnmanagedSaveDataRector::class);
    $rectorConfig->rule(FileModifyPermissionsRector::class);
    $rectorConfig->rule(FileExistsRenameRector::class);
};
