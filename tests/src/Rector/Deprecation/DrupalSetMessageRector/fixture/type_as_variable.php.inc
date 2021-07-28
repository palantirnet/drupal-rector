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

    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // This needs to be replaced, but Rector was not yet able to replace this because the type of message was set with a variable. If you need to continue to use a variable, you might consider using a switch statement.
    // @noRector
    drupal_set_message($message, $type);
}
?>
