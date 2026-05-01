<?php

// Class extending Time (a short-name CachePluginBase subclass) — cacheExpire() must be removed.
use Drupal\views\Plugin\views\cache\Time;

class MyTimedCache extends Time
{
    protected function cacheExpire($type)
    {
        return 3600;
    }

    protected function cacheSetMaxAge($type)
    {
        return 3600;
    }
}
?>
-----
<?php

// Class extending Time (a short-name CachePluginBase subclass) — cacheExpire() must be removed.
use Drupal\views\Plugin\views\cache\Time;

class MyTimedCache extends Time
{
    protected function cacheSetMaxAge($type)
    {
        return 3600;
    }
}
?>
