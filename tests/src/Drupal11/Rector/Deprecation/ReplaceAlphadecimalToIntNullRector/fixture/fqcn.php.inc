<?php

// Static call using the fully-qualified class name (no use statement).
$a = \Drupal\Component\Utility\Number::alphadecimalToInt(NULL);
$b = \Drupal\Component\Utility\Number::alphadecimalToInt('');

?>
-----
<?php

// Static call using the fully-qualified class name (no use statement).
$a = 0;
$b = 0;

?>
