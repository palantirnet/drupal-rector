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
    $password = \Drupal\Core\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '9.1.0', function () {
        return user_password();
    }, function () {
        return \Drupal::service('password_generator')->generate();
    });
    $other_password = \Drupal\Core\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '9.1.0', function () {
        return user_password(8);
    }, function () {
        return \Drupal::service('password_generator')->generate(8);
    });
    $password_length = 12;
    $last_password = \Drupal\Core\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '9.1.0', function () use ($password_length) {
        return user_password($password_length);
    }, function () use ($password_length) {
        return \Drupal::service('password_generator')->generate($password_length);
    });
}
?>
