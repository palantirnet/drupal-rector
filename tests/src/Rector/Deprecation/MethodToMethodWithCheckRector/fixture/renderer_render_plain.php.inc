<?php

// DI with RendererInterface type hint (constructor injection).
function di_interface_example(\Drupal\Core\Render\RendererInterface $renderer) {
    $elements = ['#markup' => 'Hello'];
    $renderer->renderPlain($elements);
}

// \Drupal::service() with @var RendererInterface docblock.
function service_locator_example() {
    /** @var \Drupal\Core\Render\RendererInterface $renderer */
    $renderer = \Drupal::service('renderer');
    $elements = ['#markup' => 'Hello'];
    $renderer->renderPlain($elements);
}
?>
-----
<?php

// DI with RendererInterface type hint (constructor injection).
function di_interface_example(\Drupal\Core\Render\RendererInterface $renderer) {
    $elements = ['#markup' => 'Hello'];
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.3.0', fn() => $renderer->renderInIsolation($elements), fn() => $renderer->renderPlain($elements));
}

// \Drupal::service() with @var RendererInterface docblock.
function service_locator_example() {
    /** @var \Drupal\Core\Render\RendererInterface $renderer */
    $renderer = \Drupal::service('renderer');
    $elements = ['#markup' => 'Hello'];
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.3.0', fn() => $renderer->renderInIsolation($elements), fn() => $renderer->renderPlain($elements));
}
?>
