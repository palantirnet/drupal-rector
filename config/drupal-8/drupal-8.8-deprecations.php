<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\EntityTypeGetLowercaseLabelRector;
use DrupalRector\Rector\Deprecation\FileDefaultSchemeRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\Deprecation\PathAliasManagerServiceNameRector;
use DrupalRector\Rector\Deprecation\PathAliasRepositoryRector;
use DrupalRector\Rector\Deprecation\PathAliasWhitelistServiceNameRector;
use DrupalRector\Rector\Deprecation\PathProcessorAliasServiceNameRector;
use DrupalRector\Rector\Deprecation\PathSubscriberServiceNameRector;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->rule(PathAliasManagerServiceNameRector::class);
    $rectorConfig->rule(PathAliasWhitelistServiceNameRector::class);
    $rectorConfig->rule(PathSubscriberServiceNameRector::class);
    $rectorConfig->rule(PathProcessorAliasServiceNameRector::class);
    $rectorConfig->rule(PathAliasRepositoryRector::class);
    $rectorConfig->rule(FileDefaultSchemeRector::class);

    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class,
    [
        // https://www.drupal.org/node/2835616
        new FunctionToServiceConfiguration('entity_get_display', 'entity_display.repository', 'getViewDisplay'),
        // https://www.drupal.org/node/2835616
        new FunctionToServiceConfiguration('entity_get_form_display', 'entity_display.repository', 'getFormDisplay'),
        // https://www.drupal.org/node/3039255
        new FunctionToServiceConfiguration('file_directory_temp', 'file_system', 'getTempDirectory'),
        // https://www.drupal.org/node/3038437
        new FunctionToServiceConfiguration('file_scan_directory', 'file_system', 'scanDirectory'),
        // https://www.drupal.org/node/3035273
        new FunctionToServiceConfiguration('file_uri_target', 'stream_wrapper_manager', 'getTarget'),
    ]);

    $rectorConfig->ruleWithConfiguration(
        EntityTypeGetLowercaseLabelRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
    ]);
};
