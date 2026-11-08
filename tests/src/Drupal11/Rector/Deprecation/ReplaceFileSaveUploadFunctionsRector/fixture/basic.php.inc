<?php

$file = file_save_upload('upload', $validators, FALSE, 0);
$files = file_managed_file_save_upload($element, $form_state);
$result = _file_save_upload_from_form($element, $form_state, 0);
?>
-----
<?php

$file = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal::service(\Drupal\file\Upload\FormFileUploader::class)->saveFormUploadedFiles('upload', $validators, 'temporary://', 0), fn() => file_save_upload('upload', $validators, FALSE, 0));
$files = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal::service(\Drupal\file\Upload\ManagedFileElementHelper::class)->managedFileSaveUpload($element, $form_state), fn() => file_managed_file_save_upload($element, $form_state));
$result = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal::service(\Drupal\file\Upload\ManagedFileElementHelper::class)->saveFileUploads($element, $form_state, 0), fn() => _file_save_upload_from_form($element, $form_state, 0));
?>
