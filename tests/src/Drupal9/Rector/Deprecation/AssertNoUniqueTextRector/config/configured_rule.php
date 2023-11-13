<?php

declare(strict_types=1);

use DrupalRector\Drupal9\Rector\Deprecation\AssertNoUniqueTextRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(AssertNoUniqueTextRector::class, $rectorConfig);
};
