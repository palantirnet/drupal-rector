<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\ViewsPluginHandlerManagerRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(ViewsPluginHandlerManagerRector::class, $rectorConfig, false);
};
