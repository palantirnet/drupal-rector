<?php

use Drupal\Core\Utility\Error;

// Result used inline as a function argument.
set_error_handler(Error::currentErrorHandler());

// Result used in a boolean expression.
if (Error::currentErrorHandler() !== null) {
    // do something
}

?>
-----
<?php

use Drupal\Core\Utility\Error;

// Result used inline as a function argument.
set_error_handler(\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => get_error_handler(), fn() => Error::currentErrorHandler()));

// Result used in a boolean expression.
if (\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => get_error_handler(), fn() => Error::currentErrorHandler()) !== null) {
    // do something
}

?>
