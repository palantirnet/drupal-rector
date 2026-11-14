<?php

declare(strict_types=1);

use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\RecordingHookConvertRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(RecordingHookConvertRector::class);
    $rectorConfig->fileExtensions(['module', 'theme', 'inc']);
};
