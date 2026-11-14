<?php

/**
 * Implements hook_user_cancel().
 */
function classcounter_user_cancel($edit, $account, $method)
{
    $account->block();
}
-----
<?php

/**
 * Implements hook_user_cancel().
 */
#[\Drupal\Core\Hook\Attribute\LegacyHook]
function classcounter_user_cancel($edit, $account, $method)
{
    \Drupal::service(\Drupal\classcounter\Hook\CounterHooks1::class)->userCancel($edit, $account, $method);
}
