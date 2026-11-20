<?php

namespace Drupal\singlerun\Hook;

use Drupal\Core\Url;
use Drupal\Core\Hook\Attribute\Hook;
/**
 * Hook implementations for singlerun.
 */
class SingleRunImportNamesHooks
{
    /**
     * Implements hook_help().
     */
    #[Hook('help')]
    public function help($route_name, $bytes)
    {
        return \Drupal\Core\Url::fromRoute($route_name)->toString() . \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.2.0', fn() => \Drupal\Core\StringTranslation\SizeMarkup::create($bytes), fn() => format_size($bytes));
    }
}
