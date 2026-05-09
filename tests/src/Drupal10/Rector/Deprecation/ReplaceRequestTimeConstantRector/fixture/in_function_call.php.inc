<?php

// REQUEST_TIME used as a function argument.
$date = date('Y-m-d H:i:s', REQUEST_TIME);

// REQUEST_TIME used as an array value.
$data = ['cutoff' => REQUEST_TIME];

// REQUEST_TIME in string concatenation (still a ConstFetch, so it IS transformed).
$label = 'Timestamp: ' . REQUEST_TIME;

?>
-----
<?php

// REQUEST_TIME used as a function argument.
$date = date('Y-m-d H:i:s', \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.0.0', fn() => \Drupal::time()->getRequestTime(), fn() => REQUEST_TIME));

// REQUEST_TIME used as an array value.
$data = ['cutoff' => \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.0.0', fn() => \Drupal::time()->getRequestTime(), fn() => REQUEST_TIME)];

// REQUEST_TIME in string concatenation (still a ConstFetch, so it IS transformed).
$label = 'Timestamp: ' . \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.0.0', fn() => \Drupal::time()->getRequestTime(), fn() => REQUEST_TIME);

?>
