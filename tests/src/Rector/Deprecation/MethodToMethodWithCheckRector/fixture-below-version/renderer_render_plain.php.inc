<?php

function di_interface_example(\Drupal\Core\Render\RendererInterface $renderer) {
    $elements = ['#markup' => 'Hello'];
    $renderer->renderPlain($elements);
}
?>
-----
<?php

function di_interface_example(\Drupal\Core\Render\RendererInterface $renderer) {
    $elements = ['#markup' => 'Hello'];
    $renderer->renderPlain($elements);
}
?>
