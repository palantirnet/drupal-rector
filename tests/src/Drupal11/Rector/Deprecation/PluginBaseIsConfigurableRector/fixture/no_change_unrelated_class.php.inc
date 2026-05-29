<?php

// Class does not extend PluginBase — $this->isConfigurable() must NOT be changed.
class UnrelatedPlugin {
    public function doSomething(): void {
        if ($this->isConfigurable()) {
            // handle configurable
        }
    }
}
