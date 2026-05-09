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

$a = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => 0, fn() => Number::alphadecimalToInt(NULL));
$b = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => 0, fn() => Number::alphadecimalToInt(''));
$c = Number::alphadecimalToInt('abc');
$d = OtherClass::alphadecimalToInt(NULL);

?>
