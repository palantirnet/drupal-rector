<?php

function someBasicCallSite(\Drupal\path_alias\AliasManagerInterface $aliasManager): void
{
    $aliasManager->setCacheKey('/some/path');
    $aliasManager->writeCache();
    \Drupal::messenger()->addStatus('done');
}
?>
-----
<?php

function someBasicCallSite(\Drupal\path_alias\AliasManagerInterface $aliasManager): void
{
    \Drupal::messenger()->addStatus('done');
}
?>
