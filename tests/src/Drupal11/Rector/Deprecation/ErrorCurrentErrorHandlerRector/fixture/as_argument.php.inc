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
set_error_handler(get_error_handler());

// Result used in a boolean expression.
if (get_error_handler() !== null) {
    // do something
}

?>
