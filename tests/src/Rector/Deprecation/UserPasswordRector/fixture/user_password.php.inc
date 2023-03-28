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
    if (version_compare(\Drupal::VERSION, '9.1.0', '>=')) {
        $password = \Drupal::service('password_generator')->generate();
    }
    else {
        $password = user_password();
    }
    if (version_compare(\Drupal::VERSION, '9.1.0', '>=')) {
        $other_password = \Drupal::service('password_generator')->generate(8);
    }
    else {
        $other_password = user_password(8);
    }
    $password_length = 12;
    if (version_compare(\Drupal::VERSION, '9.1.0', '>=')) {
        $last_password = \Drupal::service('password_generator')->generate($password_length);
    }
    else {
        $last_password = user_password($password_length);
    }
}
?>
