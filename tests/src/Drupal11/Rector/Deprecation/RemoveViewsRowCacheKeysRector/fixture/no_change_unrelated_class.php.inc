<?php

// An unrelated class that happens to have a getRowCacheKeys() method —
// PHPStan cannot confirm CachePluginBase, so no change.
$data = [
    '#cache' => [
        'keys' => $unrelated->getRowCacheKeys($row),
        'id' => $unrelated->getRowId($row),
    ],
];
