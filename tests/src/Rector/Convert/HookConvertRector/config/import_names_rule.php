<?php

declare(strict_types=1);

use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\CapturingHookConvertRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(CapturingHookConvertRector::class);
    $rectorConfig->fileExtensions(['module']);
    $rectorConfig->importNames(true, false);
};
