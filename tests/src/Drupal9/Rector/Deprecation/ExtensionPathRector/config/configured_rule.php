<?php

declare(strict_types=1);

use DrupalRector\Drupal9\Rector\Deprecation\ExtensionPathRector;
use DrupalRector\Drupal9\Rector\ValueObject\ExtensionPathConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(ExtensionPathRector::class, $rectorConfig, true, [
        new ExtensionPathConfiguration('drupal_get_path', 'getPath'),
        new ExtensionPathConfiguration('drupal_get_filename', 'getPathname'),
    ]);
};
