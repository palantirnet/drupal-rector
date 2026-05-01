<?php

\theme_get_setting('logo.url');
\_system_default_theme_features();

?>
-----
<?php

\Drupal::service(\Drupal\Core\Extension\ThemeSettingsProvider::class)->getSetting('logo.url');
\Drupal\Core\Extension\ThemeSettingsProvider::DEFAULT_THEME_FEATURES;

?>
