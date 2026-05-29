<?php

function example($projects, $langcodes, $uri, $source_file, $directory) {
    $files = locale_translate_get_interface_translation_files($projects, $langcodes);
    $status = locale_translation_http_check($uri);
    $result = locale_translate_delete_translation_files($projects, $langcodes);
    $downloaded = locale_translation_download_source($source_file, $directory);
}
?>
-----
<?php

function example($projects, $langcodes, $uri, $source_file, $directory) {
    $files = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\locale\File\LocaleFileManager')->getInterfaceTranslationFiles($projects, $langcodes), fn() => locale_translate_get_interface_translation_files($projects, $langcodes));
    $status = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\locale\File\LocaleFileManager')->checkRemoteFileStatus($uri), fn() => locale_translation_http_check($uri));
    $result = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\locale\File\LocaleFileManager')->deleteTranslationFiles($projects, $langcodes), fn() => locale_translate_delete_translation_files($projects, $langcodes));
    $downloaded = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\locale\File\LocaleFileManager')->downloadTranslationSource($source_file, $directory), fn() => locale_translation_download_source($source_file, $directory));
}
?>
