<?php

// Exact key match required — partial/prefixed keys not touched.
\Drupal::config('system.performance')->get('gzip');
\Drupal::config('system.performance')->get('css');
\Drupal::config('system.performance')->get('css.preprocess');
// Variable key — String_ guard prevents transformation.
\Drupal::config('system.performance')->get($key);

?>
-----
<?php

// Exact key match required — partial/prefixed keys not touched.
\Drupal::config('system.performance')->get('gzip');
\Drupal::config('system.performance')->get('css');
\Drupal::config('system.performance')->get('css.preprocess');
// Variable key — String_ guard prevents transformation.
\Drupal::config('system.performance')->get($key);

?>
