<?php

if (theme_get_setting('logo.use_default')) {
    $url = theme_get_setting('logo.url', $theme_name);
}

$features = array_keys(_system_default_theme_features());

?>
-----
<?php

if (\Drupal::service(\Drupal\Core\Extension\ThemeSettingsProvider::class)->getSetting('logo.use_default')) {
    $url = \Drupal::service(\Drupal\Core\Extension\ThemeSettingsProvider::class)->getSetting('logo.url', $theme_name);
}

$features = array_keys(\Drupal\Core\Extension\ThemeSettingsProvider::DEFAULT_THEME_FEATURES);

?>
