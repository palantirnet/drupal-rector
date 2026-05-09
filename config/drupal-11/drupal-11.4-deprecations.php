<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\DeprecatedFilterFunctionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\FilterFormatFunctionsToServiceRector;
use DrupalRector\Drupal11\Rector\Deprecation\MediaFilterFormatEditFormValidateRector;
use DrupalRector\Drupal11\Rector\Deprecation\NodeAccessRebuildFunctionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveAutomatedCronSubmitHandlerRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveCacheExpireOverrideRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveConfigSaveTrustedDataArgRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveLinkWidgetValidateTitleElementRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveSetUriCallbackRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveTrustDataCallRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveViewsRowCacheKeysRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceEntityReferenceRecursiveLimitRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceRecipeRunnerInstallModuleRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceSessionManagerDeleteRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceSystemPerformanceGzipKeyRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceViewsProceduralFunctionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\UseEntityTypeHasIntegerIdRector;
use DrupalRector\Drupal11\Rector\Deprecation\ViewsPluginHandlerManagerRector;
use DrupalRector\Rector\Deprecation\ClassConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\ConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\FunctionCallRemovalRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\ClassConstantToClassConstantConfiguration;
use DrupalRector\Rector\ValueObject\ConstantToClassConfiguration;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Rector\ValueObject\FunctionCallRemovalConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3566424
    // https://www.drupal.org/node/3566982 (change record)
    // Views::pluginManager() and Views::handlerManager() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal::service('plugin.manager.views.*') or views.plugin_managers service.
    $rectorConfig->ruleWithConfiguration(ViewsPluginHandlerManagerRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ]);

    // https://www.drupal.org/node/2473041
    // node_access_grants() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\node\NodeGrantsHelper::nodeAccessGrants().
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.4.0', 'node_access_grants', 'Drupal\node\NodeGrantsHelper', 'nodeAccessGrants', true),
    ]);

    // https://www.drupal.org/node/3533299
    // https://www.drupal.org/node/3575096 (change record)
    // node_access_rebuild() and node_access_needs_rebuild() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\node\NodeAccessRebuild service.
    $rectorConfig->ruleWithConfiguration(NodeAccessRebuildFunctionsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ]);

    // https://www.drupal.org/node/2536594
    // https://www.drupal.org/node/3035368 (change record)
    // filter_formats(), filter_get_roles_by_format(), filter_get_formats_by_role(),
    // filter_default_format(), and filter_fallback_format() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\filter\FilterFormatRepositoryInterface service.
    $rectorConfig->ruleWithConfiguration(FilterFormatFunctionsToServiceRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ]);

    // https://www.drupal.org/node/3568124
    // https://www.drupal.org/node/3566774 (change record)
    // media_filter_format_edit_form_validate() deprecated in drupal:11.4.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\media\Hook\MediaHooks::formatEditFormValidate().
    $rectorConfig->ruleWithConfiguration(MediaFilterFormatEditFormValidateRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ]);

    // https://www.drupal.org/node/3226806
    // https://www.drupal.org/node/3566536 (change record)
    // _filter_autop(), _filter_html_escape(), and _filter_html_image_secure_process()
    // deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by plugin.manager.filter createInstance() chain.
    $rectorConfig->ruleWithConfiguration(DeprecatedFilterFunctionsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ]);

    // https://www.drupal.org/node/3577376
    // SessionManager::delete() deprecated in drupal:11.4.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\Core\Session\UserSessionRepositoryInterface::deleteAll().
    $rectorConfig->ruleWithConfiguration(ReplaceSessionManagerDeleteRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ]);

    // https://www.drupal.org/node/3550054
    // CommentItemInterface::FORM_BELOW and FORM_SEPARATE_PAGE deprecated in 11.4.0,
    // removed in 13.0.0. Replaced by FormLocation enum cases.
    // https://www.drupal.org/node/3574661
    // CommentItemInterface::HIDDEN/CLOSED/OPEN and CommentInterface::ANONYMOUS_*
    // deprecated in 11.4.0, removed in 13.0.0. Replaced by CommentingStatus and
    // AnonymousContact enum cases.
    $rectorConfig->ruleWithConfiguration(ClassConstantToClassConstantRector::class, [
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'FORM_BELOW',
            'Drupal\comment\FormLocation',
            'Below',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'FORM_SEPARATE_PAGE',
            'Drupal\comment\FormLocation',
            'SeparatePage',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'HIDDEN',
            'Drupal\comment\CommentingStatus',
            'Hidden',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'CLOSED',
            'Drupal\comment\CommentingStatus',
            'Closed',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'OPEN',
            'Drupal\comment\CommentingStatus',
            'Open',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\CommentInterface',
            'ANONYMOUS_MAYNOT_CONTACT',
            'Drupal\comment\AnonymousContact',
            'Forbidden',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\CommentInterface',
            'ANONYMOUS_MAY_CONTACT',
            'Drupal\comment\AnonymousContact',
            'Allowed',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\CommentInterface',
            'ANONYMOUS_MUST_CONTACT',
            'Drupal\comment\AnonymousContact',
            'Required',
        ),
    ]);

    // https://www.drupal.org/node/3574727
    // language_configuration_element_submit() deprecated in 11.4.0, removed in 13.0.0.
    // Replaced by LanguageConfiguration::submit().
    // language_process_language_select() deprecated in 11.4.0, removed in 12.0.0.
    // Replaced by LanguageHooks::processLanguageSelect() via the service container.
    // https://www.drupal.org/node/3566792
    // ckeditor5_filter_format_edit_form_submit() and _update_ckeditor5_html_filter()
    // deprecated in 11.4.0, removed in 12.0.0. Replaced by Ckeditor5Hooks service.
    // https://www.drupal.org/node/3560398
    // _dblog_get_message_types() and dblog_filters() deprecated in 11.4.0,
    // removed in 13.0.0. Replaced by DbLogFilters service.
    // https://www.drupal.org/node/3566888
    // contact_user_profile_form_submit() and contact_form_user_admin_settings_submit()
    // deprecated in 11.4.0, removed in 12.0.0. Replaced by ContactFormHooks service.
    // https://www.drupal.org/node/3548571
    // content_translation_* functions deprecated in 11.4.0, removed in 12.0.0/13.0.0.
    // https://www.drupal.org/node/3572339
    // locale_translation_batch_update_build() and locale_translation_batch_fetch_build()
    // deprecated in 11.4.0, removed in 13.0.0. Replaced by LocaleFetch service.
    // https://www.drupal.org/node/3569328
    // locale.translation.inc functions deprecated in 11.4.0, removed in 13.0.0.
    // https://www.drupal.org/node/3571400
    // menu_ui.module procedural functions deprecated in 11.4.0, removed in 12.0.0/13.0.0.
    // https://www.drupal.org/node/3568387
    // text_summary() deprecated in 11.4.0, removed in 13.0.0. Replaced by TextSummary service.
    // https://www.drupal.org/node/3582106
    // user_form_process_password_confirm() deprecated in 11.4.0, removed in 13.0.0.
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.4.0', 'language_process_language_select', 'Drupal\language\Hook\LanguageHooks', 'processLanguageSelect'),
        new FunctionToServiceConfiguration('11.4.0', 'ckeditor5_filter_format_edit_form_submit', 'Drupal\ckeditor5\Hook\Ckeditor5Hooks', 'filterFormatEditFormSubmit'),
        new FunctionToServiceConfiguration('11.4.0', '_update_ckeditor5_html_filter', 'Drupal\ckeditor5\Hook\Ckeditor5Hooks', 'updateCkeditor5HtmlFilter'),
        new FunctionToServiceConfiguration('11.4.0', '_dblog_get_message_types', 'Drupal\dblog\DbLogFilters', 'getMessageTypes'),
        new FunctionToServiceConfiguration('11.4.0', 'dblog_filters', 'Drupal\dblog\DbLogFilters', 'filters'),
        new FunctionToServiceConfiguration('11.4.0', 'contact_user_profile_form_submit', 'Drupal\contact\Hook\ContactFormHooks', 'profileFormSubmit'),
        new FunctionToServiceConfiguration('11.4.0', 'contact_form_user_admin_settings_submit', 'Drupal\contact\Hook\ContactFormHooks', 'userAdminSettingsSubmit'),
        new FunctionToServiceConfiguration('11.4.0', 'content_translation_translate_access', 'content_translation.manager', 'access'),
        new FunctionToServiceConfiguration('11.4.0', 'content_translation_enable_widget', 'Drupal\content_translation\ContentTranslationEnableTranslationPerBundle', 'getWidget'),
        new FunctionToServiceConfiguration('11.4.0', 'content_translation_language_configuration_element_process', 'Drupal\content_translation\ContentTranslationEnableTranslationPerBundle', 'configElementProcess'),
        new FunctionToServiceConfiguration('11.4.0', 'content_translation_language_configuration_element_validate', 'Drupal\content_translation\ContentTranslationEnableTranslationPerBundle', 'configElementValidate'),
        new FunctionToServiceConfiguration('11.4.0', 'content_translation_language_configuration_element_submit', 'Drupal\content_translation\ContentTranslationEnableTranslationPerBundle', 'configElementSubmit'),
        new FunctionToServiceConfiguration('11.4.0', '_content_translation_install_field_storage_definitions', 'Drupal\content_translation\Hook\ContentTranslationHooks', 'installFieldStorageDefinitions'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_batch_update_build', 'Drupal\locale\LocaleFetch', 'batchUpdateBuild'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_batch_fetch_build', 'Drupal\locale\LocaleFetch', 'batchFetchBuild'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_get_projects', 'locale.project', 'getProjects'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_clear_cache_projects', 'locale.project', 'resetCache'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_load_sources', 'Drupal\locale\LocaleSource', 'loadSources'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_build_sources', 'Drupal\locale\LocaleSource', 'buildSources'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_source_check_file', 'Drupal\locale\LocaleSource', 'sourceCheckFile'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_source_build', 'Drupal\locale\LocaleSource', 'sourceBuild'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_build_server_pattern', 'Drupal\locale\LocaleSource', 'buildServerPattern'),
        new FunctionToServiceConfiguration('11.4.0', '_menu_ui_node_save', 'Drupal\menu_ui\MenuUiUtility', 'menuUiNodeSave'),
        new FunctionToServiceConfiguration('11.4.0', 'menu_ui_get_menu_link_defaults', 'Drupal\menu_ui\MenuUiUtility', 'getMenuLinkDefaults'),
        new FunctionToServiceConfiguration('11.4.0', 'menu_ui_node_builder', 'Drupal\menu_ui\Hook\MenuUiHooks', 'nodeBuilder'),
        new FunctionToServiceConfiguration('11.4.0', 'menu_ui_form_node_form_submit', 'Drupal\menu_ui\Hook\MenuUiHooks', 'formNodeFormSubmit'),
        new FunctionToServiceConfiguration('11.4.0', 'menu_ui_form_node_type_form_validate', 'Drupal\menu_ui\Hook\MenuUiHooks', 'formNodeTypeFormValidate'),
        new FunctionToServiceConfiguration('11.4.0', 'menu_ui_form_node_type_form_builder', 'Drupal\menu_ui\Hook\MenuUiHooks', 'formNodeTypeFormBuilder'),
        new FunctionToServiceConfiguration('11.4.0', 'text_summary', 'Drupal\text\TextSummary', 'generate'),
        new FunctionToServiceConfiguration('11.4.0', 'user_form_process_password_confirm', 'Drupal\user\Hook\UserThemeHooks', 'processPasswordConfirm'),
    ]);

    // https://www.drupal.org/node/3035340
    // views_ui_contextual_links_suppress*() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // These are no-ops and can be removed.
    // https://www.drupal.org/node/3566768
    // https://www.drupal.org/node/3566774 (change record)
    // automated_cron_settings_submit() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Config saving is now handled automatically via #config_target on the interval element.
    // https://www.drupal.org/node/3566782
    // block_theme_initialize() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Logic moved to protected BlockHooks::themeInitialize(); external callers must drop the call.
    $rectorConfig->ruleWithConfiguration(FunctionCallRemovalRector::class, [
        new FunctionCallRemovalConfiguration('views_ui_contextual_links_suppress'),
        new FunctionCallRemovalConfiguration('views_ui_contextual_links_suppress_push'),
        new FunctionCallRemovalConfiguration('views_ui_contextual_links_suppress_pop'),
        new FunctionCallRemovalConfiguration('automated_cron_settings_submit'),
        new FunctionCallRemovalConfiguration('block_theme_initialize'),
        new FunctionCallRemovalConfiguration('syslog_facility_list'),
        new FunctionCallRemovalConfiguration('syslog_logging_settings_submit'),
        new FunctionCallRemovalConfiguration('taxonomy_build_node_index'),
        new FunctionCallRemovalConfiguration('taxonomy_delete_node_index'),
    ]);

    // https://www.drupal.org/node/2667040
    // https://www.drupal.org/node/3575062 (change record)
    // EntityTypeInterface::setUriCallback() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Use link templates or a route provider instead.
    $rectorConfig->rule(RemoveSetUriCallbackRector::class);

    // https://www.drupal.org/node/3498026
    // https://www.drupal.org/node/3579527 (change record)
    // RecipeRunner::installModule() deprecated in drupal:11.4.0. Use installModules() with an array.
    $rectorConfig->ruleWithConfiguration(ReplaceRecipeRunnerInstallModuleRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ]);

    // https://www.drupal.org/node/3184242
    // https://www.drupal.org/node/3526344 (change record)
    // system.performance css.gzip and js.gzip config keys deprecated in drupal:11.4.0, removed in drupal:12.0.0.
    // Replaced by css.compress and js.compress.
    $rectorConfig->ruleWithConfiguration(ReplaceSystemPerformanceGzipKeyRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ]);

    // https://www.drupal.org/node/3564937
    // https://www.drupal.org/node/3564958 (change record)
    // CachePluginBase::getRowCacheKeys() and getRowId() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Remove array items whose value is one of these calls.
    $rectorConfig->rule(RemoveViewsRowCacheKeysRector::class);

    // https://www.drupal.org/node/3576556
    // https://www.drupal.org/node/3576855 (change record)
    // CachePluginBase::cacheExpire() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Subclass overrides are dead code; remove them.
    $rectorConfig->rule(RemoveCacheExpireOverrideRector::class);

    // https://www.drupal.org/node/3347842
    // https://www.drupal.org/node/3348180 (change record)
    // trustData() deprecated in drupal:11.4.0, removed in drupal:13.0.0. Remove from fluent chains.
    // Config::save($has_trusted_data) boolean arg deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    $rectorConfig->ruleWithConfiguration(RemoveTrustDataCallRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ]);
    $rectorConfig->ruleWithConfiguration(RemoveConfigSaveTrustedDataArgRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ]);

    // https://www.drupal.org/node/3093118
    // https://www.drupal.org/node/3554139 (change record)
    // LinkWidget::validateTitleElement() deprecated in drupal:11.4.0, removed in drupal:12.0.0.
    // Validation is now handled by LinkTitleRequiredConstraint on the LinkItem field type.
    $rectorConfig->rule(RemoveLinkWidgetValidateTitleElementRector::class);

    // https://www.drupal.org/node/3566768
    // https://www.drupal.org/node/3566774 (change record)
    // $form['#submit'][] = 'automated_cron_settings_submit' deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Config saving is now handled automatically via #config_target on the interval element.
    $rectorConfig->rule(RemoveAutomatedCronSubmitHandlerRector::class);

    // https://www.drupal.org/node/3572243
    // https://www.drupal.org/node/3572594 (change record)
    // views_view_is_enabled(), views_view_is_disabled(), views_enable_view(),
    // views_disable_view(), views_get_view_result() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by OO equivalents on the view object or Views::getViewResult().
    $rectorConfig->ruleWithConfiguration(ReplaceViewsProceduralFunctionsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ]);

    // https://www.drupal.org/node/3566801
    // https://www.drupal.org/node/3566814 (change record)
    // getEntityTypeIdKeyType() === 'integer', entityTypeSupportsComments(), and hasIntegerId($entityType)
    // deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by EntityTypeInterface::hasIntegerId() called on the entity type object.
    $rectorConfig->ruleWithConfiguration(UseEntityTypeHasIntegerIdRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ]);

    // https://www.drupal.org/node/3568144
    // editor_filter_xss() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal::service('element.editor')->filterXss().
    // https://www.drupal.org/node/3570917
    // editor_image_upload_settings_form() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal::service(EditorImageUploadSettings::class)->getForm().
    // https://www.drupal.org/node/2907780
    // field_purge_batch() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal::service(FieldPurger::class)->purgeBatch().
    // https://www.drupal.org/node/3570839
    // _media_library_media_type_form_submit() and _media_library_views_form_media_library_after_build()
    // deprecated in drupal:11.4.0, removed in drupal:12.0.0. Replaced by MediaLibraryHooks service.
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.4.0', 'editor_filter_xss', 'element.editor', 'filterXss'),
        new FunctionToServiceConfiguration('11.4.0', 'editor_image_upload_settings_form', 'Drupal\editor\EditorImageUploadSettings', 'getForm'),
        new FunctionToServiceConfiguration('11.4.0', 'field_purge_batch', 'Drupal\Core\Field\FieldPurger', 'purgeBatch'),
        new FunctionToServiceConfiguration('11.4.0', '_media_library_media_type_form_submit', 'Drupal\media_library\Hook\MediaLibraryHooks', 'mediaTypeFormSubmit'),
        new FunctionToServiceConfiguration('11.4.0', '_media_library_views_form_media_library_after_build', 'Drupal\media_library\Hook\MediaLibraryHooks', 'viewsFormAfterBuild'),
    ]);

    // https://www.drupal.org/node/3570839
    // _media_library_configure_form_display() and _media_library_configure_view_display()
    // deprecated in drupal:11.4.0, removed in drupal:12.0.0.
    // Replaced by MediaLibraryDisplayManager static methods.
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('11.4.0', '_media_library_configure_form_display', 'Drupal\media_library\MediaLibraryDisplayManager', 'configureFormDisplay'),
        new FunctionToStaticConfiguration('11.4.0', '_media_library_configure_view_display', 'Drupal\media_library\MediaLibraryDisplayManager', 'configureViewDisplay'),
    ]);

    // https://www.drupal.org/node/3574727
    // language_configuration_element_submit() deprecated in 11.4.0, removed in 13.0.0.
    // Replaced by LanguageConfiguration::submit().
    // https://www.drupal.org/node/3035340
    // views_ui/admin.inc static trait functions deprecated in 11.4.0, removed in 13.0.0.
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('11.4.0', 'language_configuration_element_submit', 'Drupal\language\Element\LanguageConfiguration', 'submit'),
        new FunctionToStaticConfiguration('11.4.0', 'views_ui_form_button_was_clicked', 'Drupal\views\ViewsFormHelperTrait', 'formButtonWasClicked'),
        new FunctionToStaticConfiguration('11.4.0', 'views_ui_add_limited_validation', 'Drupal\views\ViewsFormAjaxHelperTrait', 'addLimitedValidation'),
        new FunctionToStaticConfiguration('11.4.0', 'views_ui_add_ajax_wrapper', 'Drupal\views\ViewsFormAjaxHelperTrait', 'addAjaxWrapper'),
        new FunctionToStaticConfiguration('11.4.0', 'views_ui_nojs_submit', 'Drupal\views\ViewsFormAjaxHelperTrait', 'noJsSubmit'),
    ]);

    // https://www.drupal.org/node/3568087
    // contextual_links_to_id() and contextual_id_to_links() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by ContextualLinksSerializer service.
    // https://www.drupal.org/node/3567618
    // image_path_flush() and image_style_options() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by ImageDerivativeUtilities service.
    // https://www.drupal.org/node/3577671
    // locale_translate_get_interface_translation_files() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by LocaleFileManager::getInterfaceTranslationFiles().
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.4.0', 'contextual_links_to_id', 'Drupal\contextual\ContextualLinksSerializer', 'linksToId'),
        new FunctionToServiceConfiguration('11.4.0', 'contextual_id_to_links', 'Drupal\contextual\ContextualLinksSerializer', 'idToLinks'),
        new FunctionToServiceConfiguration('11.4.0', 'image_path_flush', 'Drupal\image\ImageDerivativeUtilities', 'pathFlush'),
        new FunctionToServiceConfiguration('11.4.0', 'image_style_options', 'Drupal\image\ImageDerivativeUtilities', 'styleOptions'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translate_get_interface_translation_files', 'Drupal\locale\File\LocaleFileManager', 'getInterfaceTranslationFiles'),
    ]);

    // https://www.drupal.org/node/3567618
    // IMAGE_DERIVATIVE_TOKEN deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\image\ImageStyleInterface::TOKEN.
    $rectorConfig->ruleWithConfiguration(ConstantToClassConstantRector::class, [
        new ConstantToClassConfiguration('IMAGE_DERIVATIVE_TOKEN', 'Drupal\image\ImageStyleInterface', 'TOKEN'),
    ]);

    // https://www.drupal.org/node/2940605
    // EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by literal 20.
    $rectorConfig->ruleWithConfiguration(ReplaceEntityReferenceRecursiveLimitRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ]);
};
