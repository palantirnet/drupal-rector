<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\EntityDeleteMultipleRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    //$rectorConfig->rule(EntityDeleteMultipleRector::class);
    DeprecationBase::addClass(EntityDeleteMultipleRector::class, $rectorConfig, FALSE);
};
