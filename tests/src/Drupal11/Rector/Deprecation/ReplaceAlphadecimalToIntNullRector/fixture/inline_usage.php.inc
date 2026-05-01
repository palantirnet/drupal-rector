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
foo(0);
$x = 0 + 5;

?>
