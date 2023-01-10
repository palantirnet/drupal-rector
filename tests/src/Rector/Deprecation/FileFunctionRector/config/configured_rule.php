<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FileBuildUriRector;
use DrupalRector\Rector\Deprecation\FileCopyRector;
use DrupalRector\Rector\Deprecation\FileMoveRector;
use DrupalRector\Rector\Deprecation\FileSaveDataRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(FileBuildUriRector::class, $rectorConfig, FALSE);
    DeprecationBase::addClass(FileCopyRector::class, $rectorConfig, FALSE);
    DeprecationBase::addClass(FileMoveRector::class, $rectorConfig, FALSE);
    DeprecationBase::addClass(FileSaveDataRector::class, $rectorConfig, FALSE);
};
