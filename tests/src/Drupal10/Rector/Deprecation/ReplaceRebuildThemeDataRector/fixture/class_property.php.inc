<?php

use Drupal\Core\Extension\ThemeHandlerInterface;

class ThemeService {
    public function __construct(
        protected ThemeHandlerInterface $themeHandler,
    ) {}

    public function rebuildAll(): array {
        return $this->themeHandler->rebuildThemeData();
    }
}
?>
-----
<?php

use Drupal\Core\Extension\ThemeHandlerInterface;

class ThemeService {
    public function __construct(
        protected ThemeHandlerInterface $themeHandler,
    ) {}

    public function rebuildAll(): array {
        return \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.3.0', fn() => \Drupal::service('extension.list.theme')->reset()->getList(), fn() => $this->themeHandler->rebuildThemeData());
    }
}
?>
