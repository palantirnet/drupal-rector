<?php

$css = $this->config('system.performance')->get('css.gzip');
$js = $this->config('system.performance')->get('js.gzip');
$this->config('system.performance')->set('css.gzip', TRUE);

?>
-----
<?php

$css = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $this->config('system.performance')->get('css.compress'), fn() => $this->config('system.performance')->get('css.gzip'));
$js = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $this->config('system.performance')->get('js.compress'), fn() => $this->config('system.performance')->get('js.gzip'));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $this->config('system.performance')->set('css.compress', TRUE), fn() => $this->config('system.performance')->set('css.gzip', TRUE));

?>
