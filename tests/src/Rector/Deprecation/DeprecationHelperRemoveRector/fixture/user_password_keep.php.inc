<?php

use Drupal\Component\Utility\DeprecationHelper;

function simple_example()
{
    $password = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.5.0', fn() => \Drupal::service('password_generator')->generate(), fn() => user_password());
    $other_password = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.5.0', fn() => \Drupal::service('password_generator')->generate(8), fn() => user_password(8));
    $password_length = 12;
    $last_password = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.5.0', fn() => \Drupal::service('password_generator')->generate($password_length), fn() => user_password($password_length));
}

?>
-----
<?php

use Drupal\Component\Utility\DeprecationHelper;

function simple_example()
{
    $password = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.5.0', fn() => \Drupal::service('password_generator')->generate(), fn() => user_password());
    $other_password = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.5.0', fn() => \Drupal::service('password_generator')->generate(8), fn() => user_password(8));
    $password_length = 12;
    $last_password = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.5.0', fn() => \Drupal::service('password_generator')->generate($password_length), fn() => user_password($password_length));
}

?>
