<?php

use Drupal\views\Plugin\views\cache\CachePluginBase;
use Drupal\views\ResultRow;

// Tests that class methods whose entire body delegates a deprecated call to a
// CachePluginBase property are removed.  Unrelated methods must be preserved.
class MetatagViewsCacheWrapper extends CachePluginBase {
    /** @var \Drupal\views\Plugin\views\cache\CachePluginBase */
    protected $plugin;

    public function getRowCacheKeys(ResultRow $row) {
        return $this->plugin->getRowCacheKeys($row);
    }

    public function getRowId(ResultRow $row) {
        return $this->plugin->getRowId($row);
    }

    public function getCacheMaxAge(): int {
        return $this->plugin->getCacheMaxAge();
    }
}
?>
-----
<?php

use Drupal\views\Plugin\views\cache\CachePluginBase;
use Drupal\views\ResultRow;

// Tests that class methods whose entire body delegates a deprecated call to a
// CachePluginBase property are removed.  Unrelated methods must be preserved.
class MetatagViewsCacheWrapper extends CachePluginBase {
    /** @var \Drupal\views\Plugin\views\cache\CachePluginBase */
    protected $plugin;

    public function getCacheMaxAge(): int {
        return $this->plugin->getCacheMaxAge();
    }
}
?>
