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

$a = 0;
$b = 0;
$c = Number::alphadecimalToInt('abc');
$d = OtherClass::alphadecimalToInt(NULL);

?>
