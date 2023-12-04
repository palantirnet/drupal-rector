<?php

declare(strict_types=1);

use DrupalRector\Drupal8\Rector\Deprecation\EntityManagerRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(EntityManagerRector::class, $rectorConfig);
};
