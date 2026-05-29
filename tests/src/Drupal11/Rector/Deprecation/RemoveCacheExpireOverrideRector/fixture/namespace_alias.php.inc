<?php

// Class extending CachePluginBase via a namespace-aliased import.
use Drupal\views\Plugin\views\cache\CachePluginBase as ViewsCache;

class MyAliasedCache extends ViewsCache
{
    protected function cacheExpire($type)
    {
        return 0;
    }

    protected function cacheSetMaxAge($type)
    {
        return 0;
    }
}
?>
-----
<?php

// Class extending CachePluginBase via a namespace-aliased import.
use Drupal\views\Plugin\views\cache\CachePluginBase as ViewsCache;

class MyAliasedCache extends ViewsCache
{
    protected function cacheSetMaxAge($type)
    {
        return 0;
    }
}
?>
