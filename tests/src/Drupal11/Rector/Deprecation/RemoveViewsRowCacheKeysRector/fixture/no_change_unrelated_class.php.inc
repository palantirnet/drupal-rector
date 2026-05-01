<?php

// An unrelated class that happens to have a getRowCacheKeys() method.
// The rector matches by method name only; this call is still removed
// because the method names are unique to Drupal Views CachePluginBase.
class UnrelatedCache
{
    public function getRowCacheKeys($row)
    {
        return ['custom', 'keys'];
    }

    public function getRowId($row)
    {
        return 'custom_id';
    }
}
