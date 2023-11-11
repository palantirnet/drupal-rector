<?php

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
    module_load_install('example');

    $module = 'simple';
    module_load_install($module);
}
?>
-----
<?php

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
    \Drupal::moduleHandler()->loadInclude('example', 'install');

    $module = 'simple';
    \Drupal::moduleHandler()->loadInclude($module, 'install');
}
?>
