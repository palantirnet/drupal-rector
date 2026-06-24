<?php

declare(strict_types=1);

namespace Drupal\menu_ui\Hook;

if (class_exists(\Drupal\menu_ui\Hook\MenuUiHooks::class)) {
    return;
}

class MenuUiHooks
{
    public function nodeBuilder($entityType, $entity, &$form, $formState): void {}

    public function formNodeFormSubmit($form, $formState): void {}

    public function formNodeTypeFormValidate(&$form, $formState): void {}

    public function formNodeTypeFormBuilder($entityType, $type, &$form, $formState): void {}
}
