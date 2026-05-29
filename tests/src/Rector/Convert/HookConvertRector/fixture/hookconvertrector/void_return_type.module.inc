<?php

/**
 * Implements hook_cache_flush().
 */
function hookconvertrector_cache_flush(): void
{
    doWork();
}
?>
-----
<?php

/**
 * Implements hook_cache_flush().
 */
#[\Drupal\Core\Hook\Attribute\LegacyHook]
function hookconvertrector_cache_flush(): void
{
    \Drupal::service(\Drupal\hookconvertrector\Hook\VoidReturnTypeHooks::class)->cacheFlush();
}
?>
