<?php

declare(strict_types=1);

namespace Drupal\Core\Menu;

if (class_exists(\Drupal\Core\Menu\MenuPreprocess::class)) {
    return;
}

class MenuPreprocess
{
    public function preprocessMenuLocalTask(&$variables): void {}

    public function preprocessMenuLocalAction(&$variables): void {}
}
