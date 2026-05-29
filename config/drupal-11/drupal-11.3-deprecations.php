<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\ErrorCurrentErrorHandlerRector;
use DrupalRector\Drupal11\Rector\Deprecation\FileManagedFileSubmitRector;
use DrupalRector\Drupal11\Rector\Deprecation\FileSystemBasenameToNativeRector;
use DrupalRector\Drupal11\Rector\Deprecation\LoadAllIncludesRector;
use DrupalRector\Drupal11\Rector\Deprecation\NodeStorageDeprecatedMethodsRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveRootFromConvertDbUrlRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceCommentPreviewConstantsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceCommentManagerGetCountNewCommentsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeAccessViewAllNodesRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeAddBodyFieldRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeModuleProceduralFunctionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeSetPreviewModeRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceThemeGetSettingRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceTwigExtensionRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceUserSessionNamePropertyRector;
use DrupalRector\Drupal11\Rector\Deprecation\ViewsConfigUpdaterClassResolverToServiceRector;
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
    // https://www.drupal.org/node/3551729 (change record)
    // CommentManagerInterface::getCountNewComments() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\history\HistoryManager::getCountNewComments().
    $rectorConfig->ruleWithConfiguration(ReplaceCommentManagerGetCountNewCommentsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3536431
    // https://www.drupal.org/node/3536432 (change record)
    // ModuleHandler::loadAllIncludes() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by an explicit foreach over getModuleList() + loadInclude().
    $rectorConfig->rule(LoadAllIncludesRector::class);

    // https://www.drupal.org/node/3396062
    // https://www.drupal.org/node/3519187 (change record)
    // NodeStorage::revisionIds() and userRevisionIds() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by equivalent entity queries.
    $rectorConfig->rule(NodeStorageDeprecatedMethodsRector::class);

    // https://www.drupal.org/node/3533083
    // node_mass_update() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\node\NodeBulkUpdate::process().
    // https://www.drupal.org/node/3547356
    // twig_render_template() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal::service(TwigThemeEngine::class)->renderTemplate().
    // twig_extension() is handled by ReplaceTwigExtensionRector below.
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.3.0', 'node_mass_update', 'Drupal\node\NodeBulkUpdate', 'process', true),
        new FunctionToServiceConfiguration('11.3.0', 'twig_render_template', 'Drupal\Core\Template\TwigThemeEngine', 'renderTemplate'),
    ]);

    // https://www.drupal.org/node/3504125
    // template_preprocess_layout() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\layout_discovery\Hook\LayoutDiscoveryThemeHooks::preprocessLayout().
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_layout', 'Drupal\layout_discovery\Hook\LayoutDiscoveryThemeHooks', 'preprocessLayout', true),
    ]);

    // https://www.drupal.org/node/1685492
    // twig_extension() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by the '.html.twig' string literal.
    $rectorConfig->ruleWithConfiguration(ReplaceTwigExtensionRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);
    $rectorConfig->ruleWithConfiguration(ReplaceNodeModuleProceduralFunctionsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3535528
    // block_content_add_body_field() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // The body field is now added via config.
    $rectorConfig->ruleWithConfiguration(FunctionCallRemovalRector::class, [
        new FunctionCallRemovalConfiguration('block_content_add_body_field'),
    ]);

    // https://www.drupal.org/node/2010202
    // comment_uri($comment) deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by $comment->permalink().
    // https://www.drupal.org/node/3531945
    // node_type_get_description($node_type) deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by $node_type->getDescription().
    $rectorConfig->ruleWithConfiguration(FunctionToFirstArgMethodRector::class, [
        new FunctionToFirstArgMethodConfiguration('11.3.0', 'comment_uri', 'permalink'),
        new FunctionToFirstArgMethodConfiguration('11.3.0', 'node_type_get_description', 'getDescription'),
    ]);

    // https://www.drupal.org/node/3038908
    // https://www.drupal.org/node/3038909 (change record)
    // node_access_view_all_nodes() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by entityTypeManager()->getAccessControlHandler('node')->checkAllGrants().
    // drupal_static_reset('node_access_view_all_nodes') replaced by node.view_all_nodes_memory_cache->deleteAll().
    $rectorConfig->ruleWithConfiguration(ReplaceNodeAccessViewAllNodesRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3548329
    // responsive_image_* functions deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal::service(ResponsiveImageBuilder::class)->method() calls.
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.3.0', '_responsive_image_build_source_attributes', 'Drupal\responsive_image\ResponsiveImageBuilder', 'buildSourceAttributes'),
        new FunctionToServiceConfiguration('11.3.0', 'responsive_image_get_image_dimensions', 'Drupal\responsive_image\ResponsiveImageBuilder', 'getImageDimensions'),
        new FunctionToServiceConfiguration('11.3.0', 'responsive_image_get_mime_type', 'Drupal\responsive_image\ResponsiveImageBuilder', 'getMimeType'),
        new FunctionToServiceConfiguration('11.3.0', '_responsive_image_image_style_url', 'Drupal\responsive_image\ResponsiveImageBuilder', 'getImageStyleUrl'),
    ]);

    // https://www.drupal.org/node/3489266
    // https://www.drupal.org/node/3516778 (change record)
    // node_add_body_field() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by $this->createBodyField() from BodyFieldCreationTrait.
    $rectorConfig->ruleWithConfiguration(ReplaceNodeAddBodyFieldRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3513856
    // https://www.drupal.org/node/3513877 (change record)
    // UserSession::$name property read deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by getAccountName().
    $rectorConfig->ruleWithConfiguration(ReplaceUserSessionNamePropertyRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3534092
    // file_system_settings_submit() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\file\Hook\FileHooks::settingsSubmit().
    // https://www.drupal.org/node/3534089
    // https://www.drupal.org/node/3534091 (change record)
    // file_managed_file_submit() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\file\Element\ManagedFile::submit().
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('11.3.0', 'file_system_settings_submit', 'Drupal\file\Hook\FileHooks', 'settingsSubmit'),
        new FunctionToStaticConfiguration('11.3.0', 'file_managed_file_submit', 'Drupal\file\Element\ManagedFile', 'submit'),
    ]);

    // https://www.drupal.org/node/3534089
    // https://www.drupal.org/node/3534091 (change record)
    // 'file_managed_file_submit' string callback deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by [\Drupal\file\Element\ManagedFile::class, 'submit'] array callable.
    $rectorConfig->ruleWithConfiguration(FileManagedFileSubmitRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3495601
    // JSONAPI_FILTER_AMONG_* global constants deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\jsonapi\JsonApiFilter::AMONG_* class constants.
    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_ALL', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_ALL', '11.3.0'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_PUBLISHED', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_PUBLISHED', '11.3.0'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_ENABLED', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_ENABLED', '11.3.0'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_OWN', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_OWN', '11.3.0'),
    ]);

    // https://www.drupal.org/node/3538277
    // https://www.drupal.org/node/3538666 (change record)
    // DRUPAL_DISABLED/OPTIONAL/REQUIRED constants (and integers 0/1/2) in setPreviewMode()
    // deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by NodePreviewMode enum cases.
    $rectorConfig->ruleWithConfiguration(ReplaceNodeSetPreviewModeRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3530461
    // https://www.drupal.org/node/3530869 (change record)
    // FileSystemInterface::basename() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by PHP native basename().
    $rectorConfig->ruleWithConfiguration(FileSystemBasenameToNativeRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3526515
    // https://www.drupal.org/node/3529500 (change record)
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
    // https://www.drupal.org/node/3511287 (change record)
    // Database::convertDbUrlToConnectionInfo($url, $root, ...) deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // The $root parameter is obsolete; remove it (shift any $include_test_drivers arg left).
    $rectorConfig->ruleWithConfiguration(RemoveRootFromConvertDbUrlRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3551450
    // workspaces.association service and WorkspaceAssociationInterface renamed in drupal:11.3.0.
    // Replaced by workspaces.tracker and WorkspaceTrackerInterface.
    //
    // https://www.drupal.org/node/3571874
    // https://www.drupal.org/node/3527501 (change record)
    // block_content\Access\* aliases removed in drupal:11.3.0. Canonical homes
    // are in Drupal\Core\Access\*. The shims remained as deprecated aliases
    // through 11.3.x. Listed here together with workspaces because both are
    // BC-safe aliases of pre-existing canonical classes — type-hint changes
    // only, runtime semantics unchanged.
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Drupal\workspaces\WorkspaceAssociationInterface' => 'Drupal\workspaces\WorkspaceTrackerInterface',
        'Drupal\workspaces\WorkspaceAssociation' => 'Drupal\workspaces\WorkspaceTracker',
        'Drupal\block_content\Access\AccessGroupAnd' => 'Drupal\Core\Access\AccessGroupAnd',
        'Drupal\block_content\Access\DependentAccessInterface' => 'Drupal\Core\Access\DependentAccessInterface',
        'Drupal\block_content\Access\RefinableDependentAccessInterface' => 'Drupal\Core\Access\RefinableDependentAccessInterface',
        'Drupal\block_content\Access\RefinableDependentAccessTrait' => 'Drupal\Core\Access\RefinableDependentAccessTrait',
    ]);

    // https://www.drupal.org/node/3538660
    // https://www.drupal.org/node/3538678 (change record)
    // Passing an int to CommentTestBase::setCommentPreview() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by Drupal\comment\CommentPreviewMode enum cases.
    $rectorConfig->ruleWithConfiguration(ReplaceCommentPreviewConstantsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3529274
    // https://www.drupal.org/node/3530638 (change record)
    // ViewsConfigUpdater registered as a service in drupal:11.3.0. Replace
    // \Drupal::classResolver(ViewsConfigUpdater::class) with
    // \Drupal::service(ViewsConfigUpdater::class) so state set via
    // setDeprecationsEnabled(FALSE) persists across hook invocations.
    $rectorConfig->ruleWithConfiguration(ViewsConfigUpdaterClassResolverToServiceRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);
};
