<?php

use Drupal\views\Views;

$filterManager = Views::handlerManager('filter');
$displayManager = Views::pluginManager('display');
$type = 'sort';
$manager = Views::pluginManager($type);
?>
-----
<?php

use Drupal\views\Views;

$filterManager = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('plugin.manager.views.filter'), fn() => Views::handlerManager('filter'));
$displayManager = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('plugin.manager.views.display'), fn() => Views::pluginManager('display'));
$type = 'sort';
$manager = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('views.plugin_managers')->get($type), fn() => Views::pluginManager($type));
?>
