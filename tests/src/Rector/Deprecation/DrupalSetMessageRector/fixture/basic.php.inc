<?php

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
    drupal_set_message('example message');
}
?>
-----
<?php

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
    \Drupal::messenger()->addStatus('example message');
}
?>
