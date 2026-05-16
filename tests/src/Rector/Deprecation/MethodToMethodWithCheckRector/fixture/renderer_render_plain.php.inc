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
    $renderer->renderInIsolation($elements);
}

// \Drupal::service() with @var RendererInterface docblock.
function service_locator_example() {
    /** @var \Drupal\Core\Render\RendererInterface $renderer */
    $renderer = \Drupal::service('renderer');
    $elements = ['#markup' => 'Hello'];
    $renderer->renderInIsolation($elements);
}
?>
