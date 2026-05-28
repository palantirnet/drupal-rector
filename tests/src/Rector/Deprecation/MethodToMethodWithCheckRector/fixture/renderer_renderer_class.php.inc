<?php

function renderer_class_example(\Drupal\Core\Render\Renderer $renderer) {
    $elements = ['#markup' => 'Hello'];
    $renderer->renderPlain($elements);
}
?>
-----
<?php

function renderer_class_example(\Drupal\Core\Render\Renderer $renderer) {
    $elements = ['#markup' => 'Hello'];
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.3.0', fn() => $renderer->renderInIsolation($elements), fn() => $renderer->renderPlain($elements));
}
?>
