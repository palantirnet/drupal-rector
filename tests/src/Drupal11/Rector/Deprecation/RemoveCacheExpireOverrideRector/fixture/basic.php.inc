<?php

use Drupal\views\Plugin\views\cache\CachePluginBase;

class MyCache extends CachePluginBase
{
    protected function cacheExpire($type)
    {
        return \Drupal::time()->getRequestTime() - $this->options['lifespan'];
    }

    protected function cacheSetMaxAge($type)
    {
        return $this->options['lifespan'] ?: \Drupal\Core\Cache\Cache::PERMANENT;
    }
}

class UnrelatedClass
{
    protected function cacheExpire($type)
    {
        return 0;
    }
}

?>
-----
<?php

use Drupal\views\Plugin\views\cache\CachePluginBase;

class MyCache extends CachePluginBase
{
    protected function cacheSetMaxAge($type)
    {
        return $this->options['lifespan'] ?: \Drupal\Core\Cache\Cache::PERMANENT;
    }
}

class UnrelatedClass
{
    protected function cacheExpire($type)
    {
        return 0;
    }
}

?>
