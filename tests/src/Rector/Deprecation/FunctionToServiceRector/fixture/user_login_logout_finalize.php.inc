<?php

function example($account) {
    user_login_finalize($account);
    user_logout();
    \user_logout();
}
?>
-----
<?php

function example($account) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal::service(\Drupal\user\LoginFinalizer::class)->finalizeLogin($account), fn() => user_login_finalize($account));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal::service(\Drupal\user\LogoutFinalizer::class)->finalizeLogout(), fn() => user_logout());
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal::service(\Drupal\user\LogoutFinalizer::class)->finalizeLogout(), fn() => \user_logout());
}
?>
