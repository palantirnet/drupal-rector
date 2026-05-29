<?php

use Drupal\Component\Utility\Number;

$a = Number::alphadecimalToInt(NULL);
$b = Number::alphadecimalToInt('');
$c = Number::alphadecimalToInt('abc');
$d = OtherClass::alphadecimalToInt(NULL);

?>
-----
<?php

use Drupal\Component\Utility\Number;

$a = Number::alphadecimalToInt(NULL);
$b = Number::alphadecimalToInt('');
$c = Number::alphadecimalToInt('abc');
$d = OtherClass::alphadecimalToInt(NULL);

?>
