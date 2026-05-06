<?php

use Drupal\Component\Plugin\PluginBase;
use Drupal\views\Plugin\views\cache\CachePluginBase;

// A wrapper class that delegates isConfigurable() to an injected PluginBase
// property.  The receiver is $this->plugin (not $this), but since $this->plugin
// is typed as PluginBase the rector must still replace the deprecated call.
class PluginWrapper extends CachePluginBase {
    /** @var \Drupal\Component\Plugin\PluginBase */
    protected $plugin;

    public function isConfigurable(): bool {
        return $this->plugin->isConfigurable();
    }
}
?>
-----
<?php

use Drupal\Component\Plugin\PluginBase;
use Drupal\views\Plugin\views\cache\CachePluginBase;

// A wrapper class that delegates isConfigurable() to an injected PluginBase
// property.  The receiver is $this->plugin (not $this), but since $this->plugin
// is typed as PluginBase the rector must still replace the deprecated call.
class PluginWrapper extends CachePluginBase {
    /** @var \Drupal\Component\Plugin\PluginBase */
    protected $plugin;

    public function isConfigurable(): bool {
        return $this->plugin instanceof \Drupal\Component\Plugin\ConfigurableInterface;
    }
}
?>
