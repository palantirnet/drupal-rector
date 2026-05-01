<?php

// Result used as argument to another function
doSomethingWithEditor(editor_load($format_id));

?>
-----
<?php

// Result used as argument to another function
doSomethingWithEditor(\Drupal::entityTypeManager()->getStorage('editor')->load($format_id));

?>
