<?php

// Real-world contrib shape (e.g. tfa, change_pwd_page, simple_pass_reset):
// user_pass_rehash() nested as an argument inside hash_equals(). The inner
// call must be rewritten in place and BC-wrapped, leaving hash_equals() intact.
if (!hash_equals($hash, user_pass_rehash($account, $timestamp))) {
    throw new \Exception('Invalid');
}
?>
-----
<?php

// Real-world contrib shape (e.g. tfa, change_pwd_page, simple_pass_reset):
// user_pass_rehash() nested as an argument inside hash_equals(). The inner
// call must be rewritten in place and BC-wrapped, leaving hash_equals() intact.
if (!hash_equals($hash, \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\user\OneTimeAuthentication::class)->generateHmac($account, $timestamp), fn() => user_pass_rehash($account, $timestamp)))) {
    throw new \Exception('Invalid');
}
?>
