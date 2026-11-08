<?php

use Drupal\Core\Url;

/**
 * Implements hook_help().
 */
function hookconvertrector_help($route_name)
{
    return Url::fromRoute($route_name)->toString();
}
?>
-----
<?php

use Drupal\Core\Hook\Attribute\LegacyHook;
use Drupal\hookconvertrector\Hook\ImportNamesHooks;
use Drupal\Core\Url;

/**
 * Implements hook_help().
 */
#[LegacyHook]
function hookconvertrector_help($route_name)
{
    return Drupal::service(ImportNamesHooks::class)->help($route_name);
}
?>
