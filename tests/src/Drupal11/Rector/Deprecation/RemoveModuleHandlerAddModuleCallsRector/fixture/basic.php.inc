<?php

/** @var \Drupal\Core\Extension\ModuleHandlerInterface $module_handler */
$module_handler->addModule('mymodule', 'modules/mymodule');
$module_handler->addProfile('standard', 'core/profiles/standard');
$module_handler->moduleExists('mymodule');
?>
-----
<?php

$module_handler->moduleExists('mymodule');
?>
