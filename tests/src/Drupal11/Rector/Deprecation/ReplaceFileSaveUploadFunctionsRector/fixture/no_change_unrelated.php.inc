<?php

// Unrelated global functions that merely look similar must be left untouched.
mymodule_file_save_upload('upload', $validators);
file_save_uploads('upload', $validators);

// Argument shapes the deprecated functions never had, and call forms whose
// positional order cannot be resolved statically, are skipped rather than
// rewritten into something that would not compile.
file_save_upload();
file_managed_file_save_upload($element);
_file_save_upload_from_form($element, $form_state, 0, \Drupal\Core\File\FileExists::Replace, 'extra');
file_save_upload(form_field_name: 'upload', validators: $validators);
file_managed_file_save_upload(...$args);

// A legacy integer $fileExists went through FileExists::fromLegacyInt() inside
// the deprecated function; the service method only accepts the enum, so these
// are left for a human rather than rewritten into a TypeError.
file_save_upload('upload', $validators, FALSE, 0, 1);
_file_save_upload_from_form($element, $form_state, 0, 1);
?>
-----
<?php

// Unrelated global functions that merely look similar must be left untouched.
mymodule_file_save_upload('upload', $validators);
file_save_uploads('upload', $validators);

// Argument shapes the deprecated functions never had, and call forms whose
// positional order cannot be resolved statically, are skipped rather than
// rewritten into something that would not compile.
file_save_upload();
file_managed_file_save_upload($element);
_file_save_upload_from_form($element, $form_state, 0, \Drupal\Core\File\FileExists::Replace, 'extra');
file_save_upload(form_field_name: 'upload', validators: $validators);
file_managed_file_save_upload(...$args);

// A legacy integer $fileExists went through FileExists::fromLegacyInt() inside
// the deprecated function; the service method only accepts the enum, so these
// are left for a human rather than rewritten into a TypeError.
file_save_upload('upload', $validators, FALSE, 0, 1);
_file_save_upload_from_form($element, $form_state, 0, 1);
?>
