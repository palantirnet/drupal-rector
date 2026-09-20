<?php

$file = file_save_upload('upload', $validators, FALSE, 0);
$files = file_managed_file_save_upload($element, $form_state);
$result = _file_save_upload_from_form($element, $form_state, 0);
?>
-----
<?php

$file = file_save_upload('upload', $validators, FALSE, 0);
$files = file_managed_file_save_upload($element, $form_state);
$result = _file_save_upload_from_form($element, $form_state, 0);
?>
