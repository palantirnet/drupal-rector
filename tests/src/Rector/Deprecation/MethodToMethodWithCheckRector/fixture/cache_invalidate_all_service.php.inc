<?php

\Drupal::service('cache.render')->invalidateAll();

Drupal::service('cache.render')->invalidateAll();
?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::service('cache.render')->deleteAll(), fn() => \Drupal::service('cache.render')->invalidateAll());

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => Drupal::service('cache.render')->deleteAll(), fn() => Drupal::service('cache.render')->invalidateAll());
?>
