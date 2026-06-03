<?php

// Different method on AliasManager — must not be removed.
function unrelatedMethodCallSite(\Drupal\path_alias\AliasManagerInterface $aliasManager): string
{
    return $aliasManager->getAliasByPath('/node/1');
}
?>
-----
<?php

// Different method on AliasManager — must not be removed.
function unrelatedMethodCallSite(\Drupal\path_alias\AliasManagerInterface $aliasManager): string
{
    return $aliasManager->getAliasByPath('/node/1');
}
?>
