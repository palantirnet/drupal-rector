<?php

$css = \Drupal::config('system.performance')->get('css.gzip');
$js = \Drupal::config('system.performance')->get('js.gzip');
\Drupal::configFactory()->getEditable('system.performance')->set('css.gzip', TRUE);
// Unrelated: leave unchanged
$other = \Drupal::config('other.config')->get('css.gzip');

?>
-----
<?php

$css = \Drupal::config('system.performance')->get('css.compress');
$js = \Drupal::config('system.performance')->get('js.compress');
\Drupal::configFactory()->getEditable('system.performance')->set('css.compress', TRUE);
// Unrelated: leave unchanged
$other = \Drupal::config('other.config')->get('css.gzip');

?>
