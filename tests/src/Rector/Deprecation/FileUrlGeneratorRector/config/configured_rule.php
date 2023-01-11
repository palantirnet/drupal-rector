<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FileUrlGenerator\FileCreateUrlRector;
use DrupalRector\Rector\Deprecation\FileUrlGenerator\FileUrlTransformRelativeRector;
use DrupalRector\Rector\Deprecation\FileUrlGenerator\FromUriRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(FileCreateUrlRector::class, $rectorConfig, FALSE);
    DeprecationBase::addClass(FileUrlTransformRelativeRector::class, $rectorConfig, FALSE);
    DeprecationBase::addClass(FromUriRector::class, $rectorConfig, FALSE);
};
