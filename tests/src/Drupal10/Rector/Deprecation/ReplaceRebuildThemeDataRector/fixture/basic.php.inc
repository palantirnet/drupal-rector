<?php

/** @var \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler */
$themes = $theme_handler->rebuildThemeData();
?>
-----
<?php

/** @var \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler */
$themes = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.3.0', fn() => \Drupal::service('extension.list.theme')->reset()->getList(), fn() => $theme_handler->rebuildThemeData());
?>
