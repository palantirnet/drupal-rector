<?php

use Drupal\Core\Url;

/**
 * Implements hook_help().
 */
function singlerun_help($route_name, $bytes)
{
    return Url::fromRoute($route_name)->toString().format_size($bytes);
}
?>
-----
<?php

use Drupal\Core\Hook\Attribute\LegacyHook;
use Drupal\singlerun\Hook\SingleRunImportNamesHooks;
use Drupal\Core\Url;

/**
 * Implements hook_help().
 */
#[LegacyHook]
function singlerun_help($route_name, $bytes)
{
    return Drupal::service(SingleRunImportNamesHooks::class)->help($route_name, $bytes);
}
?>
