<?php

use Drupal\Core\Extension\ModuleHandlerInterface;

class SchemaDotOrgConverter {
    /**
     * @var \Drupal\Core\Extension\ModuleHandlerInterface
     */
    protected $moduleHandler;

    public function __construct(ModuleHandlerInterface $moduleHandler) {
        $this->moduleHandler = $moduleHandler;
    }

    public function installAll(): void {
        $this->moduleHandler->loadAllIncludes('install');
    }
}
?>
-----
<?php

use Drupal\Core\Extension\ModuleHandlerInterface;

class SchemaDotOrgConverter {
    /**
     * @var \Drupal\Core\Extension\ModuleHandlerInterface
     */
    protected $moduleHandler;

    public function __construct(ModuleHandlerInterface $moduleHandler) {
        $this->moduleHandler = $moduleHandler;
    }

    public function installAll(): void {
        foreach ($this->moduleHandler->getModuleList() as $module => $filename) {
            $this->moduleHandler->loadInclude($module, 'install');
        }
    }
}
?>
