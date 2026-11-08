<?php

// Real-world contrib shapes. gutenberg and h5p pass a variable destination and
// omit the remaining arguments; imce passes all five with the replace
// behaviour in a variable; site_verify already uses the FileExists enum.
function mymodule_upload($validators, $destination, $replace): void {
    $result = file_save_upload('upload', $validators, $destination);
    $files = file_save_upload('imce', $validators, $destination, NULL, $replace);
    $file = file_save_upload('file_upload', $validators, FALSE, 0, \Drupal\Core\File\FileExists::Replace);
}
?>
-----
<?php

// Real-world contrib shapes. gutenberg and h5p pass a variable destination and
// omit the remaining arguments; imce passes all five with the replace
// behaviour in a variable; site_verify already uses the FileExists enum.
function mymodule_upload($validators, $destination, $replace): void {
    $result = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal::service(\Drupal\file\Upload\FormFileUploader::class)->saveFormUploadedFiles('upload', $validators, $destination), fn() => file_save_upload('upload', $validators, $destination));
    $files = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal::service(\Drupal\file\Upload\FormFileUploader::class)->saveFormUploadedFiles('imce', $validators, $destination, NULL, $replace), fn() => file_save_upload('imce', $validators, $destination, NULL, $replace));
    $file = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal::service(\Drupal\file\Upload\FormFileUploader::class)->saveFormUploadedFiles('file_upload', $validators, 'temporary://', 0, \Drupal\Core\File\FileExists::Replace), fn() => file_save_upload('file_upload', $validators, FALSE, 0, \Drupal\Core\File\FileExists::Replace));
}
?>
