<?php

// Class extending CachePluginBase by fully-qualified class name (no use import).
class MyFqcnCache extends \Drupal\views\Plugin\views\cache\CachePluginBase
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

// Class extending CachePluginBase by fully-qualified class name (no use import).
class MyFqcnCache extends \Drupal\views\Plugin\views\cache\CachePluginBase
{
    protected function cacheSetMaxAge($type)
    {
        return 0;
    }
}
?>
