<?php

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
    \Drupal\Core\Extension\ModuleHandler::loadInclude('example', 'install');

    $module = 'simple';
    \Drupal\Core\Extension\ModuleHandler::loadInclude($module, 'install');
}
