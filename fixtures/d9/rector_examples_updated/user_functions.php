<?php

function user_functions() {
    // user_password().
    $password = \Drupal::service('password_generator')->generate();
    $other_password = \Drupal::service('password_generator')->generate(8);
    $password_length = 12;
    $last_password = \Drupal::service('password_generator')->generate($password_length);
}
