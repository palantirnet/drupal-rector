<?php

// addModule() used in a fluent chain cannot be removed as a statement —
// removing it would break the chain. The rector only removes standalone statements.
/** @var \Drupal\Core\Extension\ModuleHandlerInterface $module_handler */
$module_handler->addModule('mymodule', 'modules/mymodule')->getSomething();
?>
-----
<?php

// addModule() used in a fluent chain cannot be removed as a statement —
// removing it would break the chain. The rector only removes standalone statements.
/** @var \Drupal\Core\Extension\ModuleHandlerInterface $module_handler */
$module_handler->addModule('mymodule', 'modules/mymodule')->getSomething();
?>
