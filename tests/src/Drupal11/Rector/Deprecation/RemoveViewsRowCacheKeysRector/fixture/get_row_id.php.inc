<?php

// Tests removal of getRowId() used as an array item value.
$data = [
    '#cache' => [
        'keys' => $cache_plugin->getRowId($row),
        'tags' => $cache_plugin->getRowCacheTags($row),
        'max-age' => $max_age,
    ],
];

?>
-----
<?php

// Tests removal of getRowId() used as an array item value.
$data = [
    '#cache' => [
        'tags' => $cache_plugin->getRowCacheTags($row),
        'max-age' => $max_age,
    ],
];

?>
