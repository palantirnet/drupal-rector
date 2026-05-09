<?php

$cutoff = REQUEST_TIME - $lifespan;
$other = time();

?>
-----
<?php

$cutoff = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.0.0', fn() => \Drupal::time()->getRequestTime(), fn() => REQUEST_TIME) - $lifespan;
$other = time();

?>
