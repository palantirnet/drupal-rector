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

$filterManager = \Drupal::service('plugin.manager.views.filter');
$displayManager = \Drupal::service('plugin.manager.views.display');
$type = 'sort';
$manager = \Drupal::service('views.plugin_managers')->get($type);
?>
