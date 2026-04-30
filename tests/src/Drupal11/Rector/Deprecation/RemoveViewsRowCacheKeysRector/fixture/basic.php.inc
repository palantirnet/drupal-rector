<?php

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

$data = [
    '#cache' => [
        'tags' => $cache_plugin->getRowCacheTags($row),
        'max-age' => $max_age,
    ],
];

?>
