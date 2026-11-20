<?php

namespace Drupal\hookconvertrector\Hook;

use Drupal\Core\Hook\Attribute\Hook;
/**
 * Hook implementations for hookconvertrector.
 */
class ValueReturnHooks
{
    /**
     * Implements hook_node_access().
     */
    #[Hook('node_access')]
    public function nodeAccess($node, $op, $account)
    {
        return \AccessResult::neutral();
    }
}
