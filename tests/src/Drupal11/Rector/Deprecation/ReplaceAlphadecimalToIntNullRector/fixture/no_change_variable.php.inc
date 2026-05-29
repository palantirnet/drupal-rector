<?php

use Drupal\Component\Utility\Number;

// Variable argument — cannot be statically determined as null/empty, so must not be changed.
$value = null;
$result = Number::alphadecimalToInt($value);
