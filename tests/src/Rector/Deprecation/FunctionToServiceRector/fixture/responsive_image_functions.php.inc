<?php

function example($variables, $breakpoint, $multipliers, $image_style_name, $dimensions, $uri, $extension, $style_name, $path) {
    $attributes = _responsive_image_build_source_attributes($variables, $breakpoint, $multipliers);
    $dims = responsive_image_get_image_dimensions($image_style_name, $dimensions, $uri);
    $mime = responsive_image_get_mime_type($image_style_name, $extension);
    $url = _responsive_image_image_style_url($style_name, $path);
}
?>
-----
<?php

function example($variables, $breakpoint, $multipliers, $image_style_name, $dimensions, $uri, $extension, $style_name, $path) {
    $attributes = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\responsive_image\ResponsiveImageBuilder::class)->buildSourceAttributes($variables, $breakpoint, $multipliers), fn() => _responsive_image_build_source_attributes($variables, $breakpoint, $multipliers));
    $dims = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\responsive_image\ResponsiveImageBuilder::class)->getImageDimensions($image_style_name, $dimensions, $uri), fn() => responsive_image_get_image_dimensions($image_style_name, $dimensions, $uri));
    $mime = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\responsive_image\ResponsiveImageBuilder::class)->getMimeType($image_style_name, $extension), fn() => responsive_image_get_mime_type($image_style_name, $extension));
    $url = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\responsive_image\ResponsiveImageBuilder::class)->getImageStyleUrl($style_name, $path), fn() => _responsive_image_image_style_url($style_name, $path));
}
?>
