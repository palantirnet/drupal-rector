<?php

$all = system_region_list($theme);
$visible = system_region_list($theme, REGIONS_VISIBLE);
$default = system_default_region($theme);
?>
-----
<?php

$all = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('theme_handler')->getTheme($theme)->listAllRegions(), fn() => system_region_list($theme));
$visible = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('theme_handler')->getTheme($theme)->listVisibleRegions(), fn() => system_region_list($theme, REGIONS_VISIBLE));
$default = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('theme_handler')->getTheme($theme)->getDefaultRegion(), fn() => system_default_region($theme));
?>
