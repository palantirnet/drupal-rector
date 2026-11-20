<?php

namespace Drupal\hookconvertrector\Hook;

use Drupal\Core\Hook\Attribute\Hook;
/**
 * Hook implementations for hookconvertrector.
 */
class NoReturnHooks
{
    /**
     * Implements hook_user_cancel().
     */
    #[Hook('user_cancel')]
    public function userCancel($edit, $account, $method)
    {
        $account->block();
    }
}
