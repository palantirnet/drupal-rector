<?php

function someConcreteCallSite(\Drupal\path_alias\AliasManager $aliasManager): void
{
    $aliasManager->setCacheKey('/some/path');
    $aliasManager->writeCache();
    \Drupal::messenger()->addStatus('done');
}
?>
-----
<?php

function someConcreteCallSite(\Drupal\path_alias\AliasManager $aliasManager): void
{
    \Drupal::messenger()->addStatus('done');
}
?>
