<?php

user_pass_rehash($account, $timestamp);
user_pass_reset_url($account, $options);
user_cancel_url($account, $options);
?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\user\OneTimeAuthentication::class)->generateHmac($account, $timestamp), fn() => user_pass_rehash($account, $timestamp));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\user\OneTimeAuthentication::class)->generateOneTimeLoginUrl($account, $options)->toString(), fn() => user_pass_reset_url($account, $options));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\user\OneTimeAuthentication::class)->generateCancelConfirmUrl($account, $options)->toString(), fn() => user_cancel_url($account, $options));
?>
