<?php

function example($html, $format, $original_format, $editor) {
    $filtered = editor_filter_xss($html, $format, $original_format);
    $form = editor_image_upload_settings_form($editor);
}
?>
-----
<?php

function example($html, $format, $original_format, $editor) {
    $filtered = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('element.editor')->filterXss($html, $format, $original_format), fn() => editor_filter_xss($html, $format, $original_format));
    $form = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\editor\EditorImageUploadSettings')->getForm($editor), fn() => editor_image_upload_settings_form($editor));
}
?>
