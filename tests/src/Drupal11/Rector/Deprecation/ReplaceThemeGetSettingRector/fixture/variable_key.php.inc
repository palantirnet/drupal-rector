<?php

// Variable key: all args are passed through, no String_ guard on theme_get_setting.
$setting = theme_get_setting($setting_name);
$setting = theme_get_setting($setting_name, $theme);

?>
-----
<?php

// Variable key: all args are passed through, no String_ guard on theme_get_setting.
$setting = \Drupal::service(\Drupal\Core\Extension\ThemeSettingsProvider::class)->getSetting($setting_name);
$setting = \Drupal::service(\Drupal\Core\Extension\ThemeSettingsProvider::class)->getSetting($setting_name, $theme);

?>
