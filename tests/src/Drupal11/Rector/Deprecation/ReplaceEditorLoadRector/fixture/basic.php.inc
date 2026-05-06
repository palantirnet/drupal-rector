<?php

$editor = editor_load($format_id);
$other = other_function($format_id);

?>
-----
<?php

$editor = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::entityTypeManager()->getStorage('editor')->load($format_id), fn() => editor_load($format_id));
$other = other_function($format_id);

?>
