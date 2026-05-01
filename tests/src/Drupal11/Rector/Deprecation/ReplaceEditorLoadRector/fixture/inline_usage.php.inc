<?php

// Result used inline (not assigned)
print editor_load($format_id);

?>
-----
<?php

// Result used inline (not assigned)
print \Drupal::entityTypeManager()->getStorage('editor')->load($format_id);

?>
