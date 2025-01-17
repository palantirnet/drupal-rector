<?php

declare(strict_types=1);

namespace Drupal\module\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementations for module.
 */
class ModuleHooks
{
    /**
     * Implements hook_user_cancel().
     */
    #[Hook('user_cancel')]
    public function userCancel($edit, \UserInterface $account, $method)
    {
        $red = 'red';
    }
}
