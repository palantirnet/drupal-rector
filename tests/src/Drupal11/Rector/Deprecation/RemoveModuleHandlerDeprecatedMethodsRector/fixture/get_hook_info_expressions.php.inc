<?php

/** @var \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler */
doSomething($moduleHandler->getHookInfo());
foreach ($moduleHandler->getHookInfo() as $hook => $info) {
    // process hook info
}

?>
-----
<?php

/** @var \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler */
doSomething([]);
foreach ([] as $hook => $info) {
    // process hook info
}

?>
