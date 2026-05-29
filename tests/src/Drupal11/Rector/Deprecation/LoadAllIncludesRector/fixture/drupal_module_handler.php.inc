<?php

// Call via \Drupal::moduleHandler() — chained static + method receiver.
\Drupal::moduleHandler()->loadAllIncludes('install');

?>
-----
<?php

foreach (\Drupal::moduleHandler()->getModuleList() as $module => $filename) {
    \Drupal::moduleHandler()->loadInclude($module, 'install');
}

?>
