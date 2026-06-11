<?php

function example($anchor, $current_size, $new_size) {
    $offset = image_filter_keyword($anchor, $current_size, $new_size);
}
?>
-----
<?php

function example($anchor, $current_size, $new_size) {
    $offset = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => \Drupal\Component\Utility\Image::getKeywordOffset($anchor, $current_size, $new_size), fn() => image_filter_keyword($anchor, $current_size, $new_size));
}
?>
