<?php

// Result used as argument to another function
doSomethingWithEditor(editor_load($format_id));

?>
-----
<?php

// Result used as argument to another function
doSomethingWithEditor(\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::entityTypeManager()->getStorage('editor')->load($format_id), fn() => editor_load($format_id)));

?>
