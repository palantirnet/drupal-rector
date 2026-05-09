<?php

// Static call using the fully-qualified class name (no use statement).
$a = \Drupal\Component\Utility\Number::alphadecimalToInt(NULL);
$b = \Drupal\Component\Utility\Number::alphadecimalToInt('');

?>
-----
<?php

// Static call using the fully-qualified class name (no use statement).
$a = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => 0, fn() => \Drupal\Component\Utility\Number::alphadecimalToInt(NULL));
$b = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => 0, fn() => \Drupal\Component\Utility\Number::alphadecimalToInt(''));

?>
