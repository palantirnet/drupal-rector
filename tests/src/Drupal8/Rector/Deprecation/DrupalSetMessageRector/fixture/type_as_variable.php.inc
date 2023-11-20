<?php

/**
 * This shows using a variable as the message type.
 *
 * This is rare, but used in Devel.
 */
function message_type_as_variable() {
    $message = 'example message from variable';

    $type = 'warning';

    drupal_set_message($message, $type);
}
?>
-----
<?php

/**
 * This shows using a variable as the message type.
 *
 * This is rare, but used in Devel.
 */
function message_type_as_variable() {
    $message = 'example message from variable';

    $type = 'warning';

    \Drupal::messenger()->addWarning($message);
}
?>
