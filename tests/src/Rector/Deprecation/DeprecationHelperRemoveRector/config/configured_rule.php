<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\DeprecationHelperRemoveRector;
use DrupalRector\Rector\ValueObject\DeprecationHelperRemoveConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(DeprecationHelperRemoveRector::class, $rectorConfig, false, [
        new DeprecationHelperRemoveConfiguration('10.2.0'),
    ]);
};
