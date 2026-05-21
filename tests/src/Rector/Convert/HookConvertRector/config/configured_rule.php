<?php

declare(strict_types=1);

use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\TestHookConvertRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(TestHookConvertRector::class);
    $rectorConfig->fileExtensions(['module']);
};
