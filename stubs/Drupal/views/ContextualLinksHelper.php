<?php

declare(strict_types=1);

namespace Drupal\views;

if (class_exists(\Drupal\views\ContextualLinksHelper::class)) {
    return;
}

class ContextualLinksHelper
{
    public function addLinks(&$renderElement, $location, $displayId, $viewElement = NULL): void {}
}
