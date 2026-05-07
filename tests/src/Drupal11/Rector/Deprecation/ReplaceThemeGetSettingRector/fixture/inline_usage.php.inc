<?php

if (theme_get_setting('logo.use_default')) {
    $url = theme_get_setting('logo.url', $theme_name);
}

$features = array_keys(_system_default_theme_features());

?>
-----
<?php

if (\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\Core\Extension\ThemeSettingsProvider::class)->getSetting('logo.use_default'), fn() => theme_get_setting('logo.use_default'))) {
    $url = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\Core\Extension\ThemeSettingsProvider::class)->getSetting('logo.url', $theme_name), fn() => theme_get_setting('logo.url', $theme_name));
}

$features = array_keys(\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal\Core\Extension\ThemeSettingsProvider::DEFAULT_THEME_FEATURES, fn() => _system_default_theme_features()));

?>
