<?php

// locale_config_batch_refresh_name used in various expression positions.
locale_config_batch_refresh_name($names, $langcodes, $context);

$result = locale_config_batch_refresh_name($names, $langcodes, $context);

if (locale_config_batch_refresh_name($names, $langcodes, $context)) {
    doSomething();
}

$items[] = locale_config_batch_refresh_name($names, $langcodes, $context);

?>
-----
<?php

// locale_config_batch_refresh_name used in various expression positions.
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => locale_config_batch_update_config_translations($names, $langcodes, $context), fn() => locale_config_batch_refresh_name($names, $langcodes, $context));

$result = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => locale_config_batch_update_config_translations($names, $langcodes, $context), fn() => locale_config_batch_refresh_name($names, $langcodes, $context));

if (\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => locale_config_batch_update_config_translations($names, $langcodes, $context), fn() => locale_config_batch_refresh_name($names, $langcodes, $context))) {
    doSomething();
}

$items[] = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => locale_config_batch_update_config_translations($names, $langcodes, $context), fn() => locale_config_batch_refresh_name($names, $langcodes, $context));

?>
