<?php

// Drupal 11.4: IMAGE_DERIVATIVE_TOKEN → ImageStyleInterface::TOKEN
$token = IMAGE_DERIVATIVE_TOKEN;

?>
-----
<?php

// Drupal 11.4: IMAGE_DERIVATIVE_TOKEN → ImageStyleInterface::TOKEN
$token = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\image\ImageStyleInterface::TOKEN, fn() => IMAGE_DERIVATIVE_TOKEN);

?>
