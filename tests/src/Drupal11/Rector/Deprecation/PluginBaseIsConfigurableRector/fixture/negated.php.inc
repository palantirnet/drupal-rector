<?php

class MyPlugin {
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

class MyPlugin {
    public function doSomething(): void {
        if (!$this instanceof \Drupal\Component\Plugin\ConfigurableInterface) {
            return;
        }
        return $this instanceof \Drupal\Component\Plugin\ConfigurableInterface;
    }
}
?>
