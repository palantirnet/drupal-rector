<?php

/**
 * Implements hook_user_cancel().
 */
function servicesyml_user_cancel($edit, $account, $method)
{
    $account->block();
}
-----
<?php

/**
 * Implements hook_user_cancel().
 */
#[\Drupal\Core\Hook\Attribute\LegacyHook]
function servicesyml_user_cancel($edit, $account, $method)
{
    \Drupal::service(\Drupal\servicesyml\Hook\AppendHooks::class)->userCancel($edit, $account, $method);
}
