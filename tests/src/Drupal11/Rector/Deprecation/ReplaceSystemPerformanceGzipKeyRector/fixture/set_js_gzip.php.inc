<?php

\Drupal::configFactory()->getEditable('system.performance')->set('js.gzip', TRUE);
\Drupal::configFactory()->getEditable('system.performance')->set('js.gzip', FALSE);

?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::configFactory()->getEditable('system.performance')->set('js.compress', TRUE), fn() => \Drupal::configFactory()->getEditable('system.performance')->set('js.gzip', TRUE));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::configFactory()->getEditable('system.performance')->set('js.compress', FALSE), fn() => \Drupal::configFactory()->getEditable('system.performance')->set('js.gzip', FALSE));

?>
