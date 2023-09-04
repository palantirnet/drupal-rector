<?php

function user_functions() {
    // user_password().
    $password = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '9.1.0', fn() => user_password(), fn() => \Drupal::service('password_generator')->generate());
    $other_password = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '9.1.0', fn() => user_password(8), fn() => \Drupal::service('password_generator')->generate(8));
    $password_length = 12;
    $last_password = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '9.1.0', fn() => user_password($password_length), fn() => \Drupal::service('password_generator')->generate($password_length));
}
