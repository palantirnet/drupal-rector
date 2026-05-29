<?php

// Tests the variable-first pattern: the deprecated call is assigned to a
// variable that is then used as an array item value.  Both the assignment and
// the array item must be removed.
/** @var \Drupal\views\Plugin\views\cache\CachePluginBase $cache_plugin */
$keys = $cache_plugin->getRowCacheKeys($row);
$cache = [
    '#cache' => [
        'keys' => $keys,
        'contexts' => ['languages:language_interface', 'theme', 'user.permissions'],
    ],
];

?>
-----
<?php

$cache = [
    '#cache' => [
        'contexts' => ['languages:language_interface', 'theme', 'user.permissions'],
    ],
];

?>
