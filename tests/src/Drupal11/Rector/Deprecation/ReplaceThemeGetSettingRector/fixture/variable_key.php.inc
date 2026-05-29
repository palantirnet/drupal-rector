<?php

// Variable key: all args are passed through, no String_ guard on theme_get_setting.
$setting = theme_get_setting($setting_name);
$setting = theme_get_setting($setting_name, $theme);

?>
-----
<?php

// Variable key: all args are passed through, no String_ guard on theme_get_setting.
$setting = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\Core\Extension\ThemeSettingsProvider::class)->getSetting($setting_name), fn() => theme_get_setting($setting_name));
$setting = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\Core\Extension\ThemeSettingsProvider::class)->getSetting($setting_name, $theme), fn() => theme_get_setting($setting_name, $theme));

?>
