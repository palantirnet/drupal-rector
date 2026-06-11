<?php

$builder = new \Drupal\Core\Routing\RouteBuilder($dumper, $lock, $dispatcher, $moduleHandler, $controllerResolver, $checkProvider);

?>
-----
<?php

$builder = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => new \Drupal\Core\Routing\RouteBuilder($dumper, $lock, $dispatcher, $checkProvider), fn() => new \Drupal\Core\Routing\RouteBuilder($dumper, $lock, $dispatcher, $moduleHandler, $controllerResolver, $checkProvider));

?>
