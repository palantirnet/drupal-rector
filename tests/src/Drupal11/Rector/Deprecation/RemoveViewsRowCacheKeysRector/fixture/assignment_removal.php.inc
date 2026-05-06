<?php

// Tests that typed assignment statements are removed entirely.
/** @var \Drupal\views\Plugin\views\cache\CachePluginBase $cache_plugin */
$keys = $cache_plugin->getRowCacheKeys($row);
$id = $cache_plugin->getRowId($row);

?>
-----
<?php
