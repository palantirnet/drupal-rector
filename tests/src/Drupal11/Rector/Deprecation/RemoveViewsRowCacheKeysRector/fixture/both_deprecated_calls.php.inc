<?php

// Tests removal when both deprecated methods appear in the same array.
/** @var \Drupal\views\Plugin\views\cache\CachePluginBase $cache_plugin */
$data = [
    '#cache' => [
        'keys' => $cache_plugin->getRowCacheKeys($row),
        'id' => $cache_plugin->getRowId($row),
        'tags' => $cache_plugin->getRowCacheTags($row),
    ],
];

?>
-----
<?php

// Tests removal when both deprecated methods appear in the same array.
/** @var \Drupal\views\Plugin\views\cache\CachePluginBase $cache_plugin */
$data = [
    '#cache' => [
        'tags' => $cache_plugin->getRowCacheTags($row),
    ],
];

?>
