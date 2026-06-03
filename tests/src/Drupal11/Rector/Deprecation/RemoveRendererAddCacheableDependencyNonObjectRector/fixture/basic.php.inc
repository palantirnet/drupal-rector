<?php

function nonObjectDependencies(\Drupal\Core\Render\RendererInterface $renderer): array
{
    $build = [];
    $renderer->addCacheableDependency($build, false);
    $renderer->addCacheableDependency($build, 42);
    $renderer->addCacheableDependency($build, 'a string');
    $renderer->addCacheableDependency($build, null);
    $renderer->addCacheableDependency($build, ['x' => 1]);
    return $build;
}
?>
-----
<?php

function nonObjectDependencies(\Drupal\Core\Render\RendererInterface $renderer): array
{
    $build = [];
    return $build;
}
?>
