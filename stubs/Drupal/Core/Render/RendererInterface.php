<?php

declare(strict_types=1);

namespace Drupal\Core\Render;

if (interface_exists(\Drupal\Core\Render\RendererInterface::class)) {
    return;
}

interface RendererInterface
{
    public function renderPlain(&$elements);

    public function renderInIsolation(&$elements);
}
