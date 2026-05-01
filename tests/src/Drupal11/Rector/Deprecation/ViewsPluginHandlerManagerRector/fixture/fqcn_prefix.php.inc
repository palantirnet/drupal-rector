<?php

// FQCN form without a use statement is also transformed.
$filterManager = \Drupal\views\Views::handlerManager('filter');
$displayManager = \Drupal\views\Views::pluginManager('display');

?>
-----
<?php

// FQCN form without a use statement is also transformed.
$filterManager = \Drupal::service('plugin.manager.views.filter');
$displayManager = \Drupal::service('plugin.manager.views.display');

?>
