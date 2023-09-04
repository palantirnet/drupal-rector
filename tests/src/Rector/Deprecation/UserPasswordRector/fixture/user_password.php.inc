<?php

function simple_example()
{
    $password = user_password();
    $other_password = user_password(8);
    $password_length = 12;
    $last_password = user_password($password_length);
}

?>
-----
<?php

function simple_example()
{
    $password = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '9.1.0', fn() => user_password(), fn() => \Drupal::service('password_generator')->generate());
    $other_password = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '9.1.0', fn() => user_password(8), fn() => \Drupal::service('password_generator')->generate(8));
    $password_length = 12;
    $last_password = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '9.1.0', fn() => user_password($password_length), fn() => \Drupal::service('password_generator')->generate($password_length));
}

?>
