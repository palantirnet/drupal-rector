<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\EntityGetDisplayRector;
use DrupalRector\Rector\Deprecation\EntityGetFormDisplayRector;
use DrupalRector\Rector\Deprecation\EntityTypeGetLowercaseLabelRector;
use DrupalRector\Rector\Deprecation\FileDefaultSchemeRector;
use DrupalRector\Rector\Deprecation\FileDirectoryTempRector;
use DrupalRector\Rector\Deprecation\FileScanDirectoryRector;
use DrupalRector\Rector\Deprecation\FileUriTargetRector;
use DrupalRector\Rector\Deprecation\PathAliasManagerServiceNameRector;
use DrupalRector\Rector\Deprecation\PathAliasRepositoryRector;
use DrupalRector\Rector\Deprecation\PathAliasWhitelistServiceNameRector;
use DrupalRector\Rector\Deprecation\PathProcessorAliasServiceNameRector;
use DrupalRector\Rector\Deprecation\PathSubscriberServiceNameRector;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->rule(PathAliasManagerServiceNameRector::class);
    $rectorConfig->rule(PathAliasWhitelistServiceNameRector::class);
    $rectorConfig->rule(PathSubscriberServiceNameRector::class);
    $rectorConfig->rule(PathProcessorAliasServiceNameRector::class);
    $rectorConfig->rule(PathAliasRepositoryRector::class);
    $rectorConfig->rule(FileDefaultSchemeRector::class);
    $rectorConfig->rule(EntityGetDisplayRector::class);
    $rectorConfig->rule(EntityGetFormDisplayRector::class);

    $rectorConfig->ruleWithConfiguration(
        EntityTypeGetLowercaseLabelRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
    ]);

    $rectorConfig->rule(FileScanDirectoryRector::class);
    $rectorConfig->rule(FileDirectoryTempRector::class);
    $rectorConfig->rule(FileUriTargetRector::class);
};
