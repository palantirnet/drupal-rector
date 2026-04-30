<?php

$logo = theme_get_setting('logo.url');
$fav = theme_get_setting('favicon.url', 'claro');
$features = _system_default_theme_features();

?>
-----
<?php

$logo = \Drupal::service(\Drupal\Core\Extension\ThemeSettingsProvider::class)->getSetting('logo.url');
$fav = \Drupal::service(\Drupal\Core\Extension\ThemeSettingsProvider::class)->getSetting('favicon.url', 'claro');
$features = \Drupal\Core\Extension\ThemeSettingsProvider::DEFAULT_THEME_FEATURES;

?>
