<?php

$handler = \Drupal\Core\Utility\Error::currentErrorHandler();
$other = OtherClass::currentErrorHandler();

?>
-----
<?php

$handler = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => get_error_handler(), fn() => \Drupal\Core\Utility\Error::currentErrorHandler());
$other = OtherClass::currentErrorHandler();

?>
