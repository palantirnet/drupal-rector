<?php

declare(strict_types=1);

use DrupalRector\Rector\PHPUnit\ShouldCallParentMethodsRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(ShouldCallParentMethodsRector::class);
};
