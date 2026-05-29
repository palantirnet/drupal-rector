<?php

/**
 * Implements hook_node_access().
 */
function hookconvertrector_node_access($node, $op, $account)
{
    return AccessResult::neutral();
}
?>
-----
<?php

/**
 * Implements hook_node_access().
 */
#[\Drupal\Core\Hook\Attribute\LegacyHook]
function hookconvertrector_node_access($node, $op, $account)
{
    return \Drupal::service(\Drupal\hookconvertrector\Hook\ValueReturnHooks::class)->nodeAccess($node, $op, $account);
}
?>
