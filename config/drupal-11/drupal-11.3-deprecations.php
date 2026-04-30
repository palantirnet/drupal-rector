<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\LoadAllIncludesRector;
use DrupalRector\Drupal11\Rector\Deprecation\NodeStorageDeprecatedMethodsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceCommentManagerGetCountNewCommentsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceCommentUriRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceJsonApiFilterConstantsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeSetPreviewModeRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceUserSessionNamePropertyRector;
use DrupalRector\Rector\Deprecation\FunctionCallRemovalRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Rector\ValueObject\FunctionCallRemovalConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3543035
    // CommentManagerInterface::getCountNewComments() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\history\HistoryManager::getCountNewComments().
    $rectorConfig->ruleWithConfiguration(ReplaceCommentManagerGetCountNewCommentsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3536431
    // ModuleHandler::loadAllIncludes() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by an explicit foreach over getModuleList() + loadInclude().
    $rectorConfig->rule(LoadAllIncludesRector::class);

    // https://www.drupal.org/node/3396062
    // NodeStorage::revisionIds() and userRevisionIds() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by equivalent entity queries.
    $rectorConfig->rule(NodeStorageDeprecatedMethodsRector::class);

    // https://www.drupal.org/node/3571623
    // node_mass_update() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\node\NodeBulkUpdate::process().
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.3.0', 'node_mass_update', 'Drupal\node\NodeBulkUpdate', 'process'),
    ]);

    // https://www.drupal.org/node/3504005
    // block_content_add_body_field() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // The body field is now added via config.
    $rectorConfig->ruleWithConfiguration(FunctionCallRemovalRector::class, [
        new FunctionCallRemovalConfiguration('block_content_add_body_field'),
    ]);

    // https://www.drupal.org/node/2010202
    // comment_uri($comment) deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by $comment->permalink().
    $rectorConfig->rule(ReplaceCommentUriRector::class);

    // https://www.drupal.org/node/3513856
    // UserSession::$name property read deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by getAccountName().
    $rectorConfig->rule(ReplaceUserSessionNamePropertyRector::class);

    // https://www.drupal.org/node/3534092
    // file_system_settings_submit() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\file\Hook\FileHooks::settingsSubmit().
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('11.3.0', 'file_system_settings_submit', 'Drupal\file\Hook\FileHooks', 'settingsSubmit'),
    ]);

    // https://www.drupal.org/node/3495600
    // JSONAPI_FILTER_AMONG_* global constants deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\jsonapi\JsonApiFilter::AMONG_* class constants.
    $rectorConfig->rule(ReplaceJsonApiFilterConstantsRector::class);

    // https://www.drupal.org/node/3538277
    // DRUPAL_DISABLED/OPTIONAL/REQUIRED constants (and integers 0/1/2) in setPreviewMode()
    // deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by NodePreviewMode enum cases.
    $rectorConfig->rule(ReplaceNodeSetPreviewModeRector::class);
};
