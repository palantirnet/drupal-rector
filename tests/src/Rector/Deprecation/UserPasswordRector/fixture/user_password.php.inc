<?php

function simple_example() {
    $password = user_password();
    $other_password = user_password(8);
    $password_length = 12;
    $last_password = user_password($password_length);
}
?>
-----
<?php

function simple_example() {
    $password = \Drupal::service('password_generator')->generate();
    $other_password = \Drupal::service('password_generator')->generate(8);
    $password_length = 12;
    $last_password = \Drupal::service('password_generator')->generate($password_length);
}
?>
