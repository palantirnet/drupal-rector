<?php

$cutoff = REQUEST_TIME - $lifespan;
$other = time();

?>
-----
<?php

$cutoff = \Drupal::time()->getRequestTime() - $lifespan;
$other = time();

?>
