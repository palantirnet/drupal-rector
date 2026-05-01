<?php

use Drupal\Core\Extension\ModuleHandlerInterface;

class MyService {
    public function __construct(
        protected ModuleHandlerInterface $moduleHandler,
    ) {}

    public function doSomething(string $module): string {
        return $this->moduleHandler->getName($module);
    }
}
?>
-----
<?php

use Drupal\Core\Extension\ModuleHandlerInterface;

class MyService {
    public function __construct(
        protected ModuleHandlerInterface $moduleHandler,
    ) {}

    public function doSomething(string $module): string {
        return \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.3.0', fn() => \Drupal::service('extension.list.module')->getName($module), fn() => $this->moduleHandler->getName($module));
    }
}
?>
