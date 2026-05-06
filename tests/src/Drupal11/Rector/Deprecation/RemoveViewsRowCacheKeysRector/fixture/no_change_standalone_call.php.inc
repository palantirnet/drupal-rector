<?php

// $cache_plugin has no type annotation so PHPStan cannot confirm it is a
// CachePluginBase.  Without that confirmation the rector must not remove these
// assignment statements.
$keys = $cache_plugin->getRowCacheKeys($row);
$id = $cache_plugin->getRowId($row);
