<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(FunctionToStaticRector::class, $rectorConfig, false, [
        new FunctionToStaticConfiguration('8.1.0', 'file_directory_os_temp', 'Drupal\Component\FileSystem\FileSystem', 'getOsTemporaryDirectory'),
        new FunctionToStaticConfiguration('10.1.0', 'drupal_rewrite_settings', 'Drupal\Core\Site\SettingsEditor', 'rewrite', [0 => 1, 1 => 0]),
        new FunctionToStaticConfiguration('10.2.0', 'format_size', 'Drupal\Core\StringTranslation\ByteSizeMarkup', 'create'),
        new FunctionToStaticConfiguration('10.3.0', 'file_icon_class', 'Drupal\file\IconMimeTypes', 'getIconClass'),
        new FunctionToStaticConfiguration('10.3.0', 'file_icon_map', 'Drupal\file\IconMimeTypes', 'getGenericMimeType'),
        // https://www.drupal.org/node/3574727 (Drupal 11.4)
        new FunctionToStaticConfiguration('11.4.0', 'language_configuration_element_submit', 'Drupal\language\Element\LanguageConfiguration', 'submit'),
        // https://www.drupal.org/node/3035340 (Drupal 11.4)
        new FunctionToStaticConfiguration('11.4.0', 'views_ui_form_button_was_clicked', 'Drupal\views\ViewsFormHelperTrait', 'formButtonWasClicked'),
        new FunctionToStaticConfiguration('11.4.0', 'views_ui_add_limited_validation', 'Drupal\views\ViewsFormAjaxHelperTrait', 'addLimitedValidation'),
        new FunctionToStaticConfiguration('11.4.0', 'views_ui_add_ajax_wrapper', 'Drupal\views\ViewsFormAjaxHelperTrait', 'addAjaxWrapper'),
        new FunctionToStaticConfiguration('11.4.0', 'views_ui_nojs_submit', 'Drupal\views\ViewsFormAjaxHelperTrait', 'noJsSubmit'),
        // https://www.drupal.org/node/3534092 (Drupal 11.3)
        new FunctionToStaticConfiguration('11.3.0', 'file_system_settings_submit', 'Drupal\file\Hook\FileHooks', 'settingsSubmit'),
        // https://www.drupal.org/node/3534089 (Drupal 11.3)
        new FunctionToStaticConfiguration('11.3.0', 'file_managed_file_submit', 'Drupal\file\Element\ManagedFile', 'submit'),
        // https://www.drupal.org/node/3570839 (Drupal 11.4)
        new FunctionToStaticConfiguration('11.4.0', '_media_library_configure_form_display', 'Drupal\media_library\MediaLibraryDisplayManager', 'configureFormDisplay'),
        new FunctionToStaticConfiguration('11.4.0', '_media_library_configure_view_display', 'Drupal\media_library\MediaLibraryDisplayManager', 'configureViewDisplay'),
        // https://www.drupal.org/node/3495966 (Drupal 11.2)
        new FunctionToStaticConfiguration('11.2.0', 'entity_test_create_bundle', 'Drupal\entity_test\EntityTestHelper', 'createBundle'),
        new FunctionToStaticConfiguration('11.2.0', 'entity_test_delete_bundle', 'Drupal\entity_test\EntityTestHelper', 'deleteBundle'),
        // https://www.drupal.org/node/3574424 (digest issue) / https://www.drupal.org/node/3268441 (change record, Drupal 11.1)
        new FunctionToStaticConfiguration('11.1.0', 'image_filter_keyword', 'Drupal\Component\Utility\Image', 'getKeywordOffset'),
    ]);
};
