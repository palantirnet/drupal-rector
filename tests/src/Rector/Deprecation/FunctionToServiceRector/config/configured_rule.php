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
        // https://www.drupal.org/node/3489502 (Drupal 11.2)
        new FunctionToServiceConfiguration('11.2.0', '_views_field_get_entity_type_storage', 'views.field_data_provider', 'getSqlStorageForField'),
        new FunctionToServiceConfiguration('11.2.0', 'views_entity_field_label', 'entity_field.manager', 'getFieldLabels'),
        new FunctionToServiceConfiguration('11.2.0', 'views_field_default_views_data', 'views.field_data_provider', 'defaultFieldImplementation'),
        // https://www.drupal.org/node/3501136 (Drupal 11.2)
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_time', 'Drupal\Core\Datetime\DatePreprocess', 'preprocessTime'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_datetime_form', 'Drupal\Core\Datetime\DatePreprocess', 'preprocessDatetimeForm'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_datetime_wrapper', 'Drupal\Core\Datetime\DatePreprocess', 'preprocessDatetimeWrapper'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_links', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessLinks'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_container', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessContainer'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_html', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessHtml'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_page', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessPage'),
        // https://www.drupal.org/node/3574424 (digest issue) / https://www.drupal.org/node/3548329 (change record, Drupal 11.3)
        new FunctionToServiceConfiguration('11.3.0', '_responsive_image_build_source_attributes', 'Drupal\responsive_image\ResponsiveImageBuilder', 'buildSourceAttributes', true),
        new FunctionToServiceConfiguration('11.3.0', '_responsive_image_image_style_url', 'Drupal\responsive_image\ResponsiveImageBuilder', 'getImageStyleUrl', true),
        new FunctionToServiceConfiguration('11.3.0', 'responsive_image_get_image_dimensions', 'Drupal\responsive_image\ResponsiveImageBuilder', 'getImageDimensions', true),
        new FunctionToServiceConfiguration('11.3.0', 'responsive_image_get_mime_type', 'Drupal\responsive_image\ResponsiveImageBuilder', 'getMimeType', true),
        // https://www.drupal.org/node/3533083 (Drupal 11.3)
        new FunctionToServiceConfiguration('11.3.0', 'node_mass_update', 'Drupal\node\NodeBulkUpdate', 'process', true),
        // https://www.drupal.org/node/3571382 (Drupal 11.3)
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_layout', 'Drupal\layout_discovery\Hook\LayoutDiscoveryThemeHooks', 'preprocessLayout', true),
        // https://www.drupal.org/node/1685492 (Drupal 11.3)
        new FunctionToServiceConfiguration('11.3.0', 'twig_render_template', 'Drupal\Core\Template\TwigThemeEngine', 'renderTemplate'),
        // https://www.drupal.org/node/3568088 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', '_contextual_links_to_id', 'Drupal\contextual\ContextualLinksSerializer', 'linksToId'),
        new FunctionToServiceConfiguration('11.4.0', '_contextual_id_to_links', 'Drupal\contextual\ContextualLinksSerializer', 'idToLinks'),
        // https://www.drupal.org/node/3570917 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', 'editor_filter_xss', 'element.editor', 'filterXss'),
        new FunctionToServiceConfiguration('11.4.0', 'editor_image_upload_settings_form', 'Drupal\editor\EditorImageUploadSettings', 'getForm'),
        // https://www.drupal.org/node/3494023 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', 'field_purge_batch', 'Drupal\Core\Field\FieldPurger', 'purgeBatch'),
        // https://www.drupal.org/node/3567619 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', 'image_path_flush', 'Drupal\image\ImageDerivativeUtilities', 'pathFlush'),
        new FunctionToServiceConfiguration('11.4.0', 'image_style_options', 'Drupal\image\ImageDerivativeUtilities', 'styleOptions'),
        // https://www.drupal.org/node/3577675 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', 'locale_translate_get_interface_translation_files', 'Drupal\locale\File\LocaleFileManager', 'getInterfaceTranslationFiles'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_http_check', 'Drupal\locale\File\LocaleFileManager', 'checkRemoteFileStatus'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translate_delete_translation_files', 'Drupal\locale\File\LocaleFileManager', 'deleteTranslationFiles'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_download_source', 'Drupal\locale\File\LocaleFileManager', 'downloadTranslationSource'),
        // https://www.drupal.org/node/3570839 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', '_media_library_media_type_form_submit', 'Drupal\media_library\Hook\MediaLibraryHooks', 'mediaTypeFormSubmit', true),
        new FunctionToServiceConfiguration('11.4.0', '_media_library_views_form_media_library_after_build', 'Drupal\media_library\Hook\MediaLibraryHooks', 'viewsFormAfterBuild', true),
        // https://www.drupal.org/node/3574727 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', 'language_process_language_select', 'Drupal\language\Hook\LanguageHooks', 'processLanguageSelect'),
        // https://www.drupal.org/node/3566792 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', 'ckeditor5_filter_format_edit_form_submit', 'Drupal\ckeditor5\Hook\Ckeditor5Hooks', 'filterFormatEditFormSubmit'),
        new FunctionToServiceConfiguration('11.4.0', '_update_ckeditor5_html_filter', 'Drupal\ckeditor5\Hook\Ckeditor5Hooks', 'updateCkeditor5HtmlFilter'),
        // https://www.drupal.org/node/3560398 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', '_dblog_get_message_types', 'Drupal\dblog\DbLogFilters', 'getMessageTypes'),
        new FunctionToServiceConfiguration('11.4.0', 'dblog_filters', 'Drupal\dblog\DbLogFilters', 'filters'),
        // https://www.drupal.org/node/3566888 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', 'contact_user_profile_form_submit', 'Drupal\contact\Hook\ContactFormHooks', 'profileFormSubmit'),
        new FunctionToServiceConfiguration('11.4.0', 'contact_form_user_admin_settings_submit', 'Drupal\contact\Hook\ContactFormHooks', 'userAdminSettingsSubmit'),
        // https://www.drupal.org/node/3548571 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', 'content_translation_translate_access', 'content_translation.manager', 'access'),
        new FunctionToServiceConfiguration('11.4.0', 'content_translation_enable_widget', 'Drupal\content_translation\ContentTranslationEnableTranslationPerBundle', 'getWidget'),
        new FunctionToServiceConfiguration('11.4.0', 'content_translation_language_configuration_element_process', 'Drupal\content_translation\ContentTranslationEnableTranslationPerBundle', 'configElementProcess'),
        new FunctionToServiceConfiguration('11.4.0', 'content_translation_language_configuration_element_validate', 'Drupal\content_translation\ContentTranslationEnableTranslationPerBundle', 'configElementValidate'),
        new FunctionToServiceConfiguration('11.4.0', 'content_translation_language_configuration_element_submit', 'Drupal\content_translation\ContentTranslationEnableTranslationPerBundle', 'configElementSubmit'),
        new FunctionToServiceConfiguration('11.4.0', '_content_translation_install_field_storage_definitions', 'Drupal\content_translation\Hook\ContentTranslationHooks', 'installFieldStorageDefinitions'),
        // https://www.drupal.org/node/3572339 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_batch_update_build', 'Drupal\locale\LocaleFetch', 'batchUpdateBuild'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_batch_fetch_build', 'Drupal\locale\LocaleFetch', 'batchFetchBuild'),
        // https://www.drupal.org/node/3569328 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_get_projects', 'locale.project', 'getProjects'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_clear_cache_projects', 'locale.project', 'resetCache'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_load_sources', 'Drupal\locale\LocaleSource', 'loadSources'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_build_sources', 'Drupal\locale\LocaleSource', 'buildSources'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_source_check_file', 'Drupal\locale\LocaleSource', 'sourceCheckFile'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_source_build', 'Drupal\locale\LocaleSource', 'sourceBuild'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_build_server_pattern', 'Drupal\locale\LocaleSource', 'buildServerPattern'),
        // https://www.drupal.org/node/3571400 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', '_menu_ui_node_save', 'Drupal\menu_ui\MenuUiUtility', 'menuUiNodeSave'),
        new FunctionToServiceConfiguration('11.4.0', 'menu_ui_get_menu_link_defaults', 'Drupal\menu_ui\MenuUiUtility', 'getMenuLinkDefaults'),
        new FunctionToServiceConfiguration('11.4.0', 'menu_ui_node_builder', 'Drupal\menu_ui\Hook\MenuUiHooks', 'nodeBuilder'),
        new FunctionToServiceConfiguration('11.4.0', 'menu_ui_form_node_form_submit', 'Drupal\menu_ui\Hook\MenuUiHooks', 'formNodeFormSubmit'),
        new FunctionToServiceConfiguration('11.4.0', 'menu_ui_form_node_type_form_validate', 'Drupal\menu_ui\Hook\MenuUiHooks', 'formNodeTypeFormValidate'),
        new FunctionToServiceConfiguration('11.4.0', 'menu_ui_form_node_type_form_builder', 'Drupal\menu_ui\Hook\MenuUiHooks', 'formNodeTypeFormBuilder'),
        // https://www.drupal.org/node/3568387 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', 'text_summary', 'Drupal\text\TextSummary', 'generate'),
        // https://www.drupal.org/node/3582106 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', 'user_form_process_password_confirm', 'Drupal\user\Hook\UserThemeHooks', 'processPasswordConfirm'),
        // https://www.drupal.org/node/2473041 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', 'node_access_grants', 'Drupal\node\NodeGrantsHelper', 'nodeAccessGrants', true),
        // https://www.drupal.org/node/2571679 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', 'views_add_contextual_links', 'Drupal\views\ContextualLinksHelper', 'addLinks', true),
        // https://www.drupal.org/node/3567163 (Drupal 11.4)
        new FunctionToServiceConfiguration('11.4.0', 'field_ui_form_manage_field_form_submit', 'Drupal\field_ui\Hook\FieldUiHooks', 'manageFieldFormSubmit', true),
    ]);
};
