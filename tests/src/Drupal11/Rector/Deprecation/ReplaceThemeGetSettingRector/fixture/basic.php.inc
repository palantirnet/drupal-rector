<?php

$logo = theme_get_setting('logo.url');
$fav = theme_get_setting('favicon.url', 'claro');
$features = _system_default_theme_features();

?>
-----
<?php

$logo = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\Core\Extension\ThemeSettingsProvider::class)->getSetting('logo.url'), fn() => theme_get_setting('logo.url'));
$fav = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\Core\Extension\ThemeSettingsProvider::class)->getSetting('favicon.url', 'claro'), fn() => theme_get_setting('favicon.url', 'claro'));
$features = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal\Core\Extension\ThemeSettingsProvider::DEFAULT_THEME_FEATURES, fn() => _system_default_theme_features());

?>
