<?php

// When the dependency is provably an object, the rector cannot prove it does
// not implement CacheableDependencyInterface, so it leaves the call alone.
function objectDependency(\Drupal\Core\Render\RendererInterface $renderer, \stdClass $dep): void
{
    $build = [];
    $renderer->addCacheableDependency($build, $dep);
    $renderer->addCacheableDependency($build, new \stdClass());
}
?>
-----
<?php

// When the dependency is provably an object, the rector cannot prove it does
// not implement CacheableDependencyInterface, so it leaves the call alone.
function objectDependency(\Drupal\Core\Render\RendererInterface $renderer, \stdClass $dep): void
{
    $build = [];
    $renderer->addCacheableDependency($build, $dep);
    $renderer->addCacheableDependency($build, new \stdClass());
}
?>
