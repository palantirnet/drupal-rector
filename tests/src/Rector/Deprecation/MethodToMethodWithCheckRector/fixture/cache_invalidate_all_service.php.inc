<?php

\Drupal::service('cache.render')->invalidateAll();

Drupal::service('cache.render')->invalidateAll();
?>
-----
<?php

// TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
// Please confirm that `Drupal::service(...)` is an instance of `Drupal\Core\Cache\CacheBackendInterface`. Only the method name and not the class name was checked for this replacement, so this may be a false positive.
\Drupal::service('cache.render')->deleteAll();

// TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
// Please confirm that `Drupal::service(...)` is an instance of `Drupal\Core\Cache\CacheBackendInterface`. Only the method name and not the class name was checked for this replacement, so this may be a false positive.
Drupal::service('cache.render')->deleteAll();
?>
