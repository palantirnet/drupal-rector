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
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => null, fn() => $aliasManager->setCacheKey('/some/path'));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => null, fn() => $aliasManager->writeCache());
    \Drupal::messenger()->addStatus('done');
}
?>
