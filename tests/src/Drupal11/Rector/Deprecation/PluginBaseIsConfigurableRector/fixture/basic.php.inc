<?php

use Drupal\Component\Plugin\PluginBase;

class MyPlugin extends PluginBase {
    public function doSomething(): void {
        if ($this->isConfigurable()) {
            // handle configurable
        }
    }
}
?>
-----
<?php

use Drupal\Component\Plugin\PluginBase;

class MyPlugin extends PluginBase {
    public function doSomething(): void {
        if (\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => $this instanceof \Drupal\Component\Plugin\ConfigurableInterface, fn() => $this->isConfigurable())) {
            // handle configurable
        }
    }
}
?>
