<?php

/**
 * Examples that show situations where we define the type of message.
 */
function message_types() {
    drupal_set_message('example error', 'error');

    drupal_set_message('example status', 'status');

    drupal_set_message('example warning', 'warning');
}
?>
-----
<?php

/**
 * Examples that show situations where we define the type of message.
 */
function message_types() {
    \Drupal::messenger()->addError('example error');

    \Drupal::messenger()->addStatus('example status');

    \Drupal::messenger()->addWarning('example warning');
}
?>
