<?php

namespace Drupal\hookconvertrector\Hook;

use Drupal\Core\Hook\Attribute\Hook;
/**
 * Hook implementations for hookconvertrector.
 */
class HookconvertrectorHooks
{
    /**
     * Implements hook_user_cancel().
     */
    #[Hook('user_cancel', module: 'module')]
    public function moduleUserCancel($edit, \UserInterface $account, $method)
    {
        $red = 'red';
    }
}
