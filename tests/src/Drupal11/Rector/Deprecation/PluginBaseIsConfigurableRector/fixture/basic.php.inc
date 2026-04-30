<?php

class MyPlugin {
    public function doSomething(): void {
        if ($this->isConfigurable()) {
            // handle configurable
        }
    }
}
?>
-----
<?php

class MyPlugin {
    public function doSomething(): void {
        if ($this instanceof \Drupal\Component\Plugin\ConfigurableInterface) {
            // handle configurable
        }
    }
}
?>
