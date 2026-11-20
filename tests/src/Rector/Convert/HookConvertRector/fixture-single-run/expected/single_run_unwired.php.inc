<?php

namespace Drupal\singlerun\Hook;

use Drupal\Core\Hook\Attribute\Hook;
/**
 * Hook implementations for singlerun.
 */
class SingleRunHooks
{
    /**
     * Implements hook_help().
     */
    #[Hook('help')]
    public function help($route_name, $bytes)
    {
        return format_size($bytes);
    }
}
