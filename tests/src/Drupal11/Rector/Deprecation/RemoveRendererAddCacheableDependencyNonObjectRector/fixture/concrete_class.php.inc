<?php

// The concrete Renderer class implements RendererInterface, so isObjectType()
// matches and the call is removed.
function concreteRenderer(\Drupal\Core\Render\Renderer $renderer): void
{
    $build = [];
    $renderer->addCacheableDependency($build, true);
}
?>
-----
<?php

// The concrete Renderer class implements RendererInterface, so isObjectType()
// matches and the call is removed.
function concreteRenderer(\Drupal\Core\Render\Renderer $renderer): void
{
    $build = [];
}
?>
