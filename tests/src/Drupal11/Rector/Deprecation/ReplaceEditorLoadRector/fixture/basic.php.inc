<?php

$editor = editor_load($format_id);
$other = other_function($format_id);

?>
-----
<?php

$editor = \Drupal::entityTypeManager()->getStorage('editor')->load($format_id);
$other = other_function($format_id);

?>
