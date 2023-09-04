<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\RemoveDeprecationHelperRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(RemoveDeprecationHelperRector::class, $rectorConfig, FALSE, [
        new \DrupalRector\Rector\ValueObject\RemoveDeprecationHelperConfiguration(10),
    ]);
};
