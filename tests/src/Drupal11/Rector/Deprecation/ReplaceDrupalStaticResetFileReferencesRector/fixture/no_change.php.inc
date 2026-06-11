<?php

function unrelated_static_resets($key) {
    // Different static-cache key — not one of the deprecated file-reference keys.
    drupal_static_reset('some_other_cache');
    // Non-string argument — left for manual review.
    drupal_static_reset($key);
    // No argument — resets everything, unrelated to file references.
    drupal_static_reset();
    // Named argument — left for manual review.
    drupal_static_reset(name: 'file_get_file_references');
    // Unpacked argument — left for manual review.
    $args = ['file_get_file_references'];
    drupal_static_reset(...$args);
}

?>
