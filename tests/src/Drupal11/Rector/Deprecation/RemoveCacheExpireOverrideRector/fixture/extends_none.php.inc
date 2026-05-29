<?php

// Class extending None (a short-name CachePluginBase subclass) — cacheExpire() must be removed.
use Drupal\views\Plugin\views\cache\None;

class MyNoneCache extends None
{
    protected function cacheExpire($type)
    {
        return 0;
    }
}
?>
-----
<?php

// Class extending None (a short-name CachePluginBase subclass) — cacheExpire() must be removed.
use Drupal\views\Plugin\views\cache\None;

class MyNoneCache extends None
{
}
?>
