<?php

use Drupal\Component\Plugin\PluginBase;

class MyPlugin extends PluginBase {
    public function doSomething(): void {
        if (!$this->isConfigurable()) {
            return;
        }
        return $this->isConfigurable();
    }
}
?>
-----
<?php

use Drupal\Component\Plugin\PluginBase;

class MyPlugin extends PluginBase {
    public function doSomething(): void {
        if (!$this->isConfigurable()) {
            return;
        }
        return $this->isConfigurable();
    }
}
?>
