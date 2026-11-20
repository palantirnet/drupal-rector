<?php

/**
 * Implements hook_help().
 */
function singlerun_help($route_name, $bytes)
{
    return t('Size: @size', ['@size' => format_size($bytes)]);
}
?>
-----
<?php

/**
 * Implements hook_help().
 */
#[\Drupal\Core\Hook\Attribute\LegacyHook]
function singlerun_help($route_name, $bytes)
{
    return \Drupal::service(\Drupal\singlerun\Hook\TranslationHooks::class)->help($route_name, $bytes);
}
?>
