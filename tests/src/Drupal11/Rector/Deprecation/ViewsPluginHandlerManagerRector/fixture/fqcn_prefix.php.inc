<?php

// FQCN form without a use statement is also transformed.
$filterManager = \Drupal\views\Views::handlerManager('filter');
$displayManager = \Drupal\views\Views::pluginManager('display');

?>
-----
<?php

// FQCN form without a use statement is also transformed.
$filterManager = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('plugin.manager.views.filter'), fn() => \Drupal\views\Views::handlerManager('filter'));
$displayManager = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('plugin.manager.views.display'), fn() => \Drupal\views\Views::pluginManager('display'));

?>
