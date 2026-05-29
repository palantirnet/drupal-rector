<?php

/** @var \Drupal\Core\Cache\CacheBackendInterface $cache */
$cache->invalidateAll();
?>
-----
<?php

/** @var \Drupal\Core\Cache\CacheBackendInterface $cache */
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $cache->deleteAll(), fn() => $cache->invalidateAll());
?>
