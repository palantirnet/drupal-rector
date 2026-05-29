<?php

// cacheExpire() with a body that has side effects — still removed because the
// method is deprecated and the side effects should be moved to cacheSetMaxAge().
use Drupal\views\Plugin\views\cache\CachePluginBase;

class MySideEffectsCache extends CachePluginBase
{
    protected function cacheExpire($type)
    {
        \Drupal::logger('mymodule')->notice('cacheExpire called');
        $this->options['last_expire'] = time();
        return \Drupal::time()->getRequestTime() - $this->options['lifespan'];
    }

    protected function cacheSetMaxAge($type)
    {
        return $this->options['lifespan'];
    }
}
?>
-----
<?php

// cacheExpire() with a body that has side effects — still removed because the
// method is deprecated and the side effects should be moved to cacheSetMaxAge().
use Drupal\views\Plugin\views\cache\CachePluginBase;

class MySideEffectsCache extends CachePluginBase
{
    protected function cacheSetMaxAge($type)
    {
        return $this->options['lifespan'];
    }
}
?>
