<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\EntityViewRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    //$rectorConfig->rule(EntityViewRector::class);
    DeprecationBase::addClass(EntityViewRector::class, $rectorConfig, FALSE);
};
