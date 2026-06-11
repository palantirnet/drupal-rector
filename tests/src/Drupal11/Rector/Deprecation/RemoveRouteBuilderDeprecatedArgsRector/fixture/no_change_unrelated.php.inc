<?php

// Unrelated class with the same 6-argument shape — must not be transformed.
$builder = new \Drupal\other\SomeOtherBuilder($dumper, $lock, $dispatcher, $moduleHandler, $controllerResolver, $checkProvider);

?>
