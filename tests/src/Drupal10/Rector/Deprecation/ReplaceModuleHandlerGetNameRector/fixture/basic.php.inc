<?php

/** @var \Drupal\Core\Extension\ModuleHandlerInterface $module_handler */
$name = $module_handler->getName('mymodule');
?>
-----
<?php

/** @var \Drupal\Core\Extension\ModuleHandlerInterface $module_handler */
$name = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.3.0', fn() => \Drupal::service('extension.list.module')->getName('mymodule'), fn() => $module_handler->getName('mymodule'));
?>
