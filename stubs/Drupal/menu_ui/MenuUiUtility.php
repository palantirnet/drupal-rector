<?php

declare(strict_types=1);

namespace Drupal\menu_ui;

if (class_exists(\Drupal\menu_ui\MenuUiUtility::class)) {
    return;
}

class MenuUiUtility
{
    public function menuUiNodeSave($node, $values): void {}

    public function getMenuLinkDefaults($node) {}
}
