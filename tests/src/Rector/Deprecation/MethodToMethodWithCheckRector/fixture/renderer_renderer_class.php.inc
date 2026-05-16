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
    $renderer->renderInIsolation($elements);
}
?>
