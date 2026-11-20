<?php

namespace Drupal\hookconvertrector\Hook;

use Drupal\Core\Url;
use Drupal\Core\Hook\Attribute\Hook;
/**
 * Hook implementations for hookconvertrector.
 */
class ImportNamesHooks
{
    /**
     * Implements hook_help().
     */
    #[Hook('help')]
    public function help($route_name)
    {
        return \Drupal\Core\Url::fromRoute($route_name)->toString();
    }
}
