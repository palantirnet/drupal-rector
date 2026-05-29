<?php

declare(strict_types=1);

namespace Drupal\Core\Render;

if (class_exists(\Drupal\Core\Render\Renderer::class)) {
    return;
}

class Renderer implements RendererInterface
{
    public function renderPlain(&$elements)
    {
    }

    public function renderInIsolation(&$elements)
    {
    }
}
