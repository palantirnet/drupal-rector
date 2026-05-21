<?php

/**
 * Implements hook_user_cancel().
 */
function hookconvertrector_user_cancel($edit, $account, $method)
{
    $account->block();
}
?>
-----
<?php

/**
 * Implements hook_user_cancel().
 */
#[\Drupal\Core\Hook\Attribute\LegacyHook]
function hookconvertrector_user_cancel($edit, $account, $method)
{
    \Drupal::service(\Drupal\hookconvertrector\Hook\NoReturnHooks::class)->userCancel($edit, $account, $method);
}
?>
