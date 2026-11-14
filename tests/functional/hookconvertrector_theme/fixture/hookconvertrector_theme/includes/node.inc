<?php

/**
 * Implements hook_preprocess_node().
 */
function hookconvertrector_theme_preprocess_node(array &$variables) {
    $variables['label_visible'] = FALSE;
}
