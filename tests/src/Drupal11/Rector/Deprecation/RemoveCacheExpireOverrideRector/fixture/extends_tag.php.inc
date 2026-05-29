<?php

// Class extending Tag (a short-name CachePluginBase subclass) — cacheExpire() must be removed.
use Drupal\views\Plugin\views\cache\Tag;

class MyTaggedCache extends Tag
{
    protected function cacheExpire($type)
    {
        return 0;
    }
}
?>
-----
<?php

// Class extending Tag (a short-name CachePluginBase subclass) — cacheExpire() must be removed.
use Drupal\views\Plugin\views\cache\Tag;

class MyTaggedCache extends Tag
{
}
?>
