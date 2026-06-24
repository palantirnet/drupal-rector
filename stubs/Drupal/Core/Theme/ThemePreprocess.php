<?php

declare(strict_types=1);

namespace Drupal\Core\Theme;

if (class_exists(\Drupal\Core\Theme\ThemePreprocess::class)) {
    return;
}

class ThemePreprocess
{
    public function preprocessContainer(&$variables): void {}

    public function preprocessLinks(&$variables): void {}

    public function preprocessHtml(&$variables): void {}

    public function preprocessPage(&$variables): void {}

    public function preprocessTable(&$variables): void {}

    public function preprocessTablesortIndicator(&$variables): void {}

    public function preprocessItemList(&$variables): void {}

    public function preprocessRegion(&$variables): void {}

    public function preprocessMaintenancePage(&$variables): void {}

    public function preprocessMaintenanceTaskList(&$variables): void {}

    public function preprocessInstallPage(&$variables): void {}
}
