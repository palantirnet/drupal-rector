<?php

function example($path, $include_empty) {
    image_path_flush($path);
    $options = image_style_options($include_empty);
}
?>
-----
<?php

function example($path, $include_empty) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\image\ImageDerivativeUtilities')->pathFlush($path), fn() => image_path_flush($path));
    $options = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\image\ImageDerivativeUtilities')->styleOptions($include_empty), fn() => image_style_options($include_empty));
}
?>
