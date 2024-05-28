<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(FunctionToServiceRector::class, $rectorConfig, false, [
        new FunctionToServiceConfiguration('9.3.0', 'render', 'renderer', 'render'),
        new FunctionToServiceConfiguration('8.0.0', 'file_copy', 'file.repository', 'copy'),
        new FunctionToServiceConfiguration('9.3.0', 'file_move', 'file.repository', 'move'),
        new FunctionToServiceConfiguration('9.3.0', 'file_save_data', 'file.repository', 'writeData'),
        new FunctionToServiceConfiguration('10.1.0', 'drupal_theme_rebuild', 'theme.registry', 'reset'),
        new FunctionToServiceConfiguration('10.2.0', '_drupal_flush_css_js', 'asset.query_string', 'reset'),
    ]);
};
