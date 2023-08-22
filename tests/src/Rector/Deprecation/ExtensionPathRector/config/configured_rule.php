<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ExtensionPathRector;
use DrupalRector\Rector\ValueObject\ExtensionPathConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(ExtensionPathRector::class, $rectorConfig, TRUE, [
        new ExtensionPathConfiguration('drupal_get_path', 'getPath'),
        new ExtensionPathConfiguration('drupal_get_filename', 'getPathname'),
    ]);
};
