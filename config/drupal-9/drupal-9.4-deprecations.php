<?php

declare(strict_types=1);

use DrupalRector\Rector\FuncCall\ModuleLoadInstallRector;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->rule(ModuleLoadInstallRector::class);
};
