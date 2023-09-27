<?php

use Drupal\Component\Utility\DeprecationHelper;

function simple_example()
{
    $password = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.5.0', fn() => user_password(), fn() => \Drupal::service('password_generator')->generate());
    $other_password = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.5.0', fn() => user_password(8), fn() => \Drupal::service('password_generator')->generate(8));
    $password_length = 12;
    $last_password = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.5.0', fn() => user_password($password_length), fn() => \Drupal::service('password_generator')->generate($password_length));
}

?>
-----
<?php

use Drupal\Component\Utility\DeprecationHelper;

function simple_example()
{
    $password = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.5.0', fn() => user_password(), fn() => \Drupal::service('password_generator')->generate());
    $other_password = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.5.0', fn() => user_password(8), fn() => \Drupal::service('password_generator')->generate(8));
    $password_length = 12;
    $last_password = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.5.0', fn() => user_password($password_length), fn() => \Drupal::service('password_generator')->generate($password_length));
}

?>
