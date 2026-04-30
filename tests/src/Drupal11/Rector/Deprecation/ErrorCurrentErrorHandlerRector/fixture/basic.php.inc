<?php

$handler = \Drupal\Core\Utility\Error::currentErrorHandler();
$other = OtherClass::currentErrorHandler();

?>
-----
<?php

$handler = get_error_handler();
$other = OtherClass::currentErrorHandler();

?>
