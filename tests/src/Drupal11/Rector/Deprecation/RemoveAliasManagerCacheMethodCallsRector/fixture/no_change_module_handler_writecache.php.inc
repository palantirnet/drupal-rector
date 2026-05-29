<?php

// ModuleHandler::writeCache() also exists; must not be removed.
function moduleHandlerCallSite(\Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler): void
{
    $moduleHandler->writeCache();
}
?>
-----
<?php

// ModuleHandler::writeCache() also exists; must not be removed.
function moduleHandlerCallSite(\Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler): void
{
    $moduleHandler->writeCache();
}
?>
