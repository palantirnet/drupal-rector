<?php

$css = \Drupal::config('system.performance')->get('css.gzip');
$js = \Drupal::config('system.performance')->get('js.gzip');
\Drupal::configFactory()->getEditable('system.performance')->set('css.gzip', TRUE);
// Unrelated: leave unchanged
$other = \Drupal::config('other.config')->get('css.gzip');

?>
-----
<?php

$css = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::config('system.performance')->get('css.compress'), fn() => \Drupal::config('system.performance')->get('css.gzip'));
$js = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::config('system.performance')->get('js.compress'), fn() => \Drupal::config('system.performance')->get('js.gzip'));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::configFactory()->getEditable('system.performance')->set('css.compress', TRUE), fn() => \Drupal::configFactory()->getEditable('system.performance')->set('css.gzip', TRUE));
// Unrelated: leave unchanged
$other = \Drupal::config('other.config')->get('css.gzip');

?>
