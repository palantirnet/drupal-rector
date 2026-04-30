<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FunctionCallRemovalRector;
use DrupalRector\Rector\ValueObject\FunctionCallRemovalConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(FunctionCallRemovalRector::class, $rectorConfig, false, [
        new FunctionCallRemovalConfiguration('block_content_add_body_field'),
        new FunctionCallRemovalConfiguration('template_preprocess'),
        new FunctionCallRemovalConfiguration('update_clear_update_disk_cache'),
        new FunctionCallRemovalConfiguration('update_delete_file_if_stale'),
        new FunctionCallRemovalConfiguration('_update_manager_cache_directory'),
        new FunctionCallRemovalConfiguration('_update_manager_extract_directory'),
        new FunctionCallRemovalConfiguration('_update_manager_unique_identifier'),
        new FunctionCallRemovalConfiguration('views_ui_contextual_links_suppress'),
        new FunctionCallRemovalConfiguration('views_ui_contextual_links_suppress_push'),
        new FunctionCallRemovalConfiguration('views_ui_contextual_links_suppress_pop'),
        new FunctionCallRemovalConfiguration('automated_cron_settings_submit'),
    ]);
};
