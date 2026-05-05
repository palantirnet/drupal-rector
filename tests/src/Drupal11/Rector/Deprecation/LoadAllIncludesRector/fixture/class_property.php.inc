<?php

use Drupal\Core\Extension\ModuleHandlerInterface;

class StarterKitConverter {
    public function __construct(
        protected ModuleHandlerInterface $moduleHandler,
    ) {}

    public function installAll(): void {
        $this->moduleHandler->loadAllIncludes('install');
        $this->moduleHandler->loadAllIncludes('inc', 'admin');
    }
}
?>
-----
<?php

use Drupal\Core\Extension\ModuleHandlerInterface;

class StarterKitConverter {
    public function __construct(
        protected ModuleHandlerInterface $moduleHandler,
    ) {}

    public function installAll(): void {
        foreach ($this->moduleHandler->getModuleList() as $module => $filename) {
            $this->moduleHandler->loadInclude($module, 'install');
        }
        foreach ($this->moduleHandler->getModuleList() as $module => $filename) {
            $this->moduleHandler->loadInclude($module, 'inc', 'admin');
        }
    }
}
?>
