<?php

// RouteBuilder with an unexpected argument count — must not be transformed.
$builder = new \Drupal\Core\Routing\RouteBuilder($dumper, $lock, $dispatcher, $moduleHandler, $controllerResolver);

?>
