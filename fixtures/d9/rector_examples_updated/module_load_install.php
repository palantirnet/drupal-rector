<?php

use Drupal\Core\Extension\ModuleHandler;
/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
    ModuleHandler::loadInclude('example', 'install');

    $module = 'simple';
    ModuleHandler::loadInclude($module, 'install');
}
