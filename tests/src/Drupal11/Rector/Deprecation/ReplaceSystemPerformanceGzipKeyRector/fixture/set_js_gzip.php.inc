<?php

\Drupal::configFactory()->getEditable('system.performance')->set('js.gzip', TRUE);
\Drupal::configFactory()->getEditable('system.performance')->set('js.gzip', FALSE);

?>
-----
<?php

\Drupal::configFactory()->getEditable('system.performance')->set('js.compress', TRUE);
\Drupal::configFactory()->getEditable('system.performance')->set('js.compress', FALSE);

?>
