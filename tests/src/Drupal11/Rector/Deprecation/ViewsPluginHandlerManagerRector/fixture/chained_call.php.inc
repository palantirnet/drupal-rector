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
$handler = \Drupal::service('plugin.manager.views.field')->getHandler($options, $id);

// pluginManager result used in a method chain.
$instance = \Drupal::service('plugin.manager.views.display')->createInstance($id);

?>
