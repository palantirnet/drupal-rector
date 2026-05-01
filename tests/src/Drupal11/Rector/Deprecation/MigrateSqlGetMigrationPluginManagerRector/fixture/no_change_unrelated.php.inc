<?php

// Unrelated class — getMigrationPluginManager() must NOT be rewritten.
class MyIdMap
{
    public function doSomething(): void
    {
        $manager = $this->getMigrationPluginManager();
    }
}
