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
        if ($this instanceof \Drupal\Component\Plugin\ConfigurableInterface) {
            // handle configurable
        }
    }
}
?>
