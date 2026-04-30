<?php

/** @var \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler */
$moduleHandler->writeCache();
$hookInfo = $moduleHandler->getHookInfo();
$untyped->writeCache();

?>
-----
<?php

$hookInfo = [];
$untyped->writeCache();

?>
