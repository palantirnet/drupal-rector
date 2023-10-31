<?php

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
    module_load_install('example');

    $module = 'simple';
    module_load_install($module);
}
