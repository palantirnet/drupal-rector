<?php

/**
 * Implements hook_help().
 */
function singlerun_help($route_name, $bytes)
{
    return format_size($bytes);
}

/**
 * Not a hook, only a helper.
 */
function singlerun_helper($bytes)
{
    return format_size($bytes);
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
    return \Drupal::service(\Drupal\singlerun\Hook\SingleRunHooks::class)->help($route_name, $bytes);
}

/**
 * Not a hook, only a helper.
 */
function singlerun_helper($bytes)
{
    return \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.2.0', fn() => \Drupal\Core\StringTranslation\SizeMarkup::create($bytes), fn() => format_size($bytes));
}
?>
