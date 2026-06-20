<?php

// The ?? FALSE replacement must be parenthesized when negated or used as an
// argument, so operator precedence matches the original single-call contract.
if (!user_load_by_name($name)) {
    return;
}
do_something(user_load_by_mail($mail));
?>
-----
<?php

// The ?? FALSE replacement must be parenthesized when negated or used as an
// argument, so operator precedence matches the original single-call contract.
if (!(array_values(\Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['name' => $name]))[0] ?? FALSE)) {
    return;
}
do_something(array_values(\Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['mail' => $mail]))[0] ?? FALSE);
?>
