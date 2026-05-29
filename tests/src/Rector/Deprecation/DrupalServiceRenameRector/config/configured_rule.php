<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\DrupalServiceRenameRector;
use DrupalRector\Rector\ValueObject\DrupalServiceRenameConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(DrupalServiceRenameRector::class, $rectorConfig, true, [
        new DrupalServiceRenameConfiguration('11.4.0', 'old.service', 'new.service'),
    ]);
};
