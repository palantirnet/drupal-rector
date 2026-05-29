<?php

/** @var \Drupal\views\Plugin\views\cache\CachePluginBase $cache_plugin */
$data = [
    '#cache' => [
        'keys' => $cache_plugin->getRowCacheKeys($row),
        'tags' => $cache_plugin->getRowCacheTags($row),
        'max-age' => $max_age,
    ],
];

?>
-----
<?php

/** @var \Drupal\views\Plugin\views\cache\CachePluginBase $cache_plugin */
$data = [
    '#cache' => [
        'tags' => $cache_plugin->getRowCacheTags($row),
        'max-age' => $max_age,
    ],
];

?>
