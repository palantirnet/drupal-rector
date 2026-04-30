<?php

/** @var \Drupal\Core\Cache\CacheBackendInterface $cache */
$cache->invalidateAll();
?>
-----
<?php

/** @var \Drupal\Core\Cache\CacheBackendInterface $cache */
$cache->deleteAll();
?>
