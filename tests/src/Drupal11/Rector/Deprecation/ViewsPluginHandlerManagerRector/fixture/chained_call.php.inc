<?php

use Drupal\views\Views;

// handlerManager result used immediately in a method chain.
$handler = Views::handlerManager('field')->getHandler($options, $id);

// pluginManager result used in a method chain.
$instance = Views::pluginManager('display')->createInstance($id);

?>
-----
<?php

use Drupal\views\Views;

// handlerManager result used immediately in a method chain.
$handler = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('plugin.manager.views.field'), fn() => Views::handlerManager('field'))->getHandler($options, $id);

// pluginManager result used in a method chain.
$instance = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('plugin.manager.views.display'), fn() => Views::pluginManager('display'))->createInstance($id);

?>
