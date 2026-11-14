<?php

namespace Drupal\hookconvertrector_theme\Hook;

use Drupal\Core\Hook\Attribute\Hook;
/**
 * Hook implementations for hookconvertrector_theme.
 */
class NodeHooks
{
    /**
     * Implements hook_preprocess_node().
     */
    #[Hook('preprocess_node')]
    public function preprocessNode(array &$variables)
    {
        $variables['label_visible'] = FALSE;
    }
}
