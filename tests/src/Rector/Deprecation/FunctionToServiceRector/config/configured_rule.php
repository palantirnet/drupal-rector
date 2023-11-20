<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(FunctionToServiceRector::class, $rectorConfig, false, [
        new FunctionToServiceConfiguration('render', 'renderer', 'render'),
        new FunctionToServiceConfiguration('file_copy', 'file.repository', 'copy'),
        new FunctionToServiceConfiguration('file_move', 'file.repository', 'move'),
        new FunctionToServiceConfiguration('file_save_data', 'file.repository', 'writeData'),
    ]);
};
