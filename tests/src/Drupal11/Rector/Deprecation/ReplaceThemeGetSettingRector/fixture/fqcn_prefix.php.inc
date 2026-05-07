<?php

\theme_get_setting('logo.url');
\_system_default_theme_features();

?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\Core\Extension\ThemeSettingsProvider::class)->getSetting('logo.url'), fn() => \theme_get_setting('logo.url'));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal\Core\Extension\ThemeSettingsProvider::DEFAULT_THEME_FEATURES, fn() => \_system_default_theme_features());

?>
