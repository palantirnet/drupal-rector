<?php

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
    module_load_install('example');

    $module = 'simple';
    module_load_install($module);

    $type = 'install';
    $module = 'example';
    $name = 'name';
    module_load_include($type, $module, $name);
    module_load_include($type, $module);

    module_load_include('install', 'example', 'name');
    module_load_include('install', 'example');
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

    $type = 'install';
    $module = 'example';
    $name = 'name';
    \Drupal::moduleHandler()->loadInclude($module, $type, $name);
    \Drupal::moduleHandler()->loadInclude($module, $type);

    \Drupal::moduleHandler()->loadInclude('example', 'install', 'name');
    \Drupal::moduleHandler()->loadInclude('example', 'install');
}
?>
