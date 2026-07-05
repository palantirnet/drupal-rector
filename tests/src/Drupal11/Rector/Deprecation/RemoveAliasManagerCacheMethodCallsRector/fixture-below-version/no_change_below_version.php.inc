<?php

// Target Drupal is below 11.3.0: the methods are not yet deprecated and still
// perform real caching work, so the rector must not touch the call.
function belowVersionCallSite(\Drupal\path_alias\AliasManagerInterface $aliasManager): void
{
    $aliasManager->setCacheKey('/some/path');
    $aliasManager->writeCache();
}
?>
