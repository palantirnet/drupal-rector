<?php

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
    drupal_set_message('some message', 'info');
}
?>
-----
<?php

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
    \Drupal::messenger()->addMessage('some message', 'info');
}
?>
