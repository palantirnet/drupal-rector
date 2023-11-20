<?php

/**
 * An example using all of the arguments.
 */
function using_all_arguments() {
    drupal_set_message('example warning', 'status', TRUE);
}
?>
-----
<?php

/**
 * An example using all of the arguments.
 */
function using_all_arguments() {
    \Drupal::messenger()->addStatus('example warning', TRUE);
}
?>
