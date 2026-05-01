<?php

// A standalone method call (not an array item value) must not be changed.
// The rector only removes array items; bare method calls are unaffected.
$keys = $cache_plugin->getRowCacheKeys($row);
$id = $cache_plugin->getRowId($row);
