<?php

use Drupal\Component\Utility\Number;

// Result used inline: as a function argument and in an expression.
foo(Number::alphadecimalToInt(null));
$x = Number::alphadecimalToInt('') + 5;

?>
-----
<?php

use Drupal\Component\Utility\Number;

// Result used inline: as a function argument and in an expression.
foo(\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => 0, fn() => Number::alphadecimalToInt(null)));
$x = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => 0, fn() => Number::alphadecimalToInt('')) + 5;

?>
