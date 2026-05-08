<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\ErrorCurrentErrorHandlerRector;
use DrupalRector\Drupal11\Rector\Deprecation\FileSystemBasenameToNativeRector;
use DrupalRector\Drupal11\Rector\Deprecation\LoadAllIncludesRector;
use DrupalRector\Drupal11\Rector\Deprecation\NodeStorageDeprecatedMethodsRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveRootFromConvertDbUrlRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceCommentManagerGetCountNewCommentsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeAccessViewAllNodesRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeAddBodyFieldRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeModuleProceduralFunctionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeSetPreviewModeRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceThemeGetSettingRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceTwigExtensionRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceUserSessionNamePropertyRector;
use DrupalRector\Rector\Deprecation\ConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\FunctionCallRemovalRector;
use DrupalRector\Rector\Deprecation\FunctionToFirstArgMethodRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\ConstantToClassConfiguration;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Rector\ValueObject\FunctionCallRemovalConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToFirstArgMethodConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

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
    // node_type_get_names() and node_get_type_label() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // https://www.drupal.org/node/1685492
    // twig_render_template() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal::service(TwigThemeEngine::class)->renderTemplate().
    // twig_extension() is handled by ReplaceTwigExtensionRector below.
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.3.0', 'node_mass_update', 'Drupal\node\NodeBulkUpdate', 'process'),
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_layout', 'Drupal\layout_discovery\Hook\LayoutDiscoveryThemeHooks', 'preprocessLayout'),
        new FunctionToServiceConfiguration('11.3.0', 'twig_render_template', 'Drupal\Core\Template\TwigThemeEngine', 'renderTemplate'),
    ]);

    // https://www.drupal.org/node/1685492
    // twig_extension() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by the '.html.twig' string literal.
    $rectorConfig->rule(ReplaceTwigExtensionRector::class);
    $rectorConfig->ruleWithConfiguration(ReplaceNodeModuleProceduralFunctionsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
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
    $rectorConfig->ruleWithConfiguration(FunctionToFirstArgMethodRector::class, [
        new FunctionToFirstArgMethodConfiguration('11.3.0', 'comment_uri', 'permalink'),
    ]);

    // https://www.drupal.org/node/3038908
    // node_access_view_all_nodes() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by entityTypeManager()->getAccessControlHandler('node')->checkAllGrants().
    // drupal_static_reset('node_access_view_all_nodes') replaced by node.view_all_nodes_memory_cache->deleteAll().
    $rectorConfig->ruleWithConfiguration(ReplaceNodeAccessViewAllNodesRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3574424
    // responsive_image_* functions deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal::service(ResponsiveImageBuilder::class)->method() calls.
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.3.0', '_responsive_image_build_source_attributes', 'Drupal\responsive_image\ResponsiveImageBuilder', 'buildSourceAttributes'),
        new FunctionToServiceConfiguration('11.3.0', 'responsive_image_get_image_dimensions', 'Drupal\responsive_image\ResponsiveImageBuilder', 'getImageDimensions'),
        new FunctionToServiceConfiguration('11.3.0', 'responsive_image_get_mime_type', 'Drupal\responsive_image\ResponsiveImageBuilder', 'getMimeType'),
        new FunctionToServiceConfiguration('11.3.0', '_responsive_image_image_style_url', 'Drupal\responsive_image\ResponsiveImageBuilder', 'getImageStyleUrl'),
    ]);

    // https://www.drupal.org/node/3489266
    // node_add_body_field() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by $this->createBodyField() from BodyFieldCreationTrait.
    $rectorConfig->ruleWithConfiguration(ReplaceNodeAddBodyFieldRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3513856
    // UserSession::$name property read deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by getAccountName().
    $rectorConfig->ruleWithConfiguration(ReplaceUserSessionNamePropertyRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3534092
    // file_system_settings_submit() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\file\Hook\FileHooks::settingsSubmit().
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('11.3.0', 'file_system_settings_submit', 'Drupal\file\Hook\FileHooks', 'settingsSubmit'),
    ]);

    // https://www.drupal.org/node/3495600
    // JSONAPI_FILTER_AMONG_* global constants deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\jsonapi\JsonApiFilter::AMONG_* class constants.
    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_ALL', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_ALL'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_PUBLISHED', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_PUBLISHED'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_ENABLED', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_ENABLED'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_OWN', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_OWN'),
    ]);

    // https://www.drupal.org/node/3538277
    // DRUPAL_DISABLED/OPTIONAL/REQUIRED constants (and integers 0/1/2) in setPreviewMode()
    // deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by NodePreviewMode enum cases.
    $rectorConfig->rule(ReplaceNodeSetPreviewModeRector::class);

    // https://www.drupal.org/node/3530461
    // FileSystemInterface::basename() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by PHP native basename().
    $rectorConfig->ruleWithConfiguration(FileSystemBasenameToNativeRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3526515
    // Error::currentErrorHandler() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by PHP built-in get_error_handler().
    $rectorConfig->ruleWithConfiguration(ErrorCurrentErrorHandlerRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3573896
    // theme_get_setting() and _system_default_theme_features() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by ThemeSettingsProvider service.
    $rectorConfig->ruleWithConfiguration(ReplaceThemeGetSettingRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3522513
    // Database::convertDbUrlToConnectionInfo($url, $root, ...) deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // The $root parameter is obsolete; remove it (shift any $include_test_drivers arg left).
    $rectorConfig->rule(RemoveRootFromConvertDbUrlRector::class);

    // https://www.drupal.org/node/3551446
    // workspaces.association service and WorkspaceAssociationInterface renamed in drupal:11.3.0.
    // Replaced by workspaces.tracker and WorkspaceTrackerInterface.
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Drupal\workspaces\WorkspaceAssociationInterface' => 'Drupal\workspaces\WorkspaceTrackerInterface',
        'Drupal\workspaces\WorkspaceAssociation' => 'Drupal\workspaces\WorkspaceTracker',
    ]);
};
