<?php

namespace Drupal\hookconvertrector\Hook;

use Drupal\Core\Hook\Attribute\Hook;
/**
 * Hook implementations for hookconvertrector.
 */
class VoidReturnTypeHooks
{
    /**
     * Implements hook_cache_flush().
     */
    #[Hook('cache_flush')]
    public function cacheFlush(): void
    {
        doWork();
    }
}
