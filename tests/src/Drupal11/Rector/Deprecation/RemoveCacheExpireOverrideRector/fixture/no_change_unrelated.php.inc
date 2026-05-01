<?php

// Class that does NOT extend CachePluginBase — cacheExpire() must NOT be removed.
class UnrelatedCacheClass
{
    protected function cacheExpire($type)
    {
        return 3600;
    }
}
