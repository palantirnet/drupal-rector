<?php

/** @var \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler */
$moduleHandler->loadAllIncludes('install');
$moduleHandler->loadAllIncludes('inc', 'admin');
?>
-----
<?php

foreach ($moduleHandler->getModuleList() as $module => $filename) {
    $moduleHandler->loadInclude($module, 'install');
}
foreach ($moduleHandler->getModuleList() as $module => $filename) {
    $moduleHandler->loadInclude($module, 'inc', 'admin');
}
?>
