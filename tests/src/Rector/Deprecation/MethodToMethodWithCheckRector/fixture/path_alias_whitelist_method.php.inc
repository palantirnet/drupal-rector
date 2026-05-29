<?php

/** @var \Drupal\path_alias\AliasManager $alias_manager */
$alias_manager->pathAliasWhitelistRebuild($path);
?>
-----
<?php

/** @var \Drupal\path_alias\AliasManager $alias_manager */
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => $alias_manager->pathAliasPrefixListRebuild($path), fn() => $alias_manager->pathAliasWhitelistRebuild($path));
?>
