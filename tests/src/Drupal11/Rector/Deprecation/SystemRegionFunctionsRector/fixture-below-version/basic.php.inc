<?php

$all = system_region_list($theme);
$all_explicit = system_region_list($theme, REGIONS_ALL);
$visible = system_region_list($theme, REGIONS_VISIBLE);
$visible_class_const = system_region_list($theme, \Drupal\block\BlockRepositoryInterface::REGIONS_VISIBLE);
$visible_string = system_region_list($theme, 'visible');
$default = system_default_region($theme);
?>
-----
<?php

$all = system_region_list($theme);
$all_explicit = system_region_list($theme, REGIONS_ALL);
$visible = system_region_list($theme, REGIONS_VISIBLE);
$visible_class_const = system_region_list($theme, \Drupal\block\BlockRepositoryInterface::REGIONS_VISIBLE);
$visible_string = system_region_list($theme, 'visible');
$default = system_default_region($theme);
?>
