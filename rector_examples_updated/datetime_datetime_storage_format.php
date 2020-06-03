<?php

/**
 * @file
 * Demonstrates updated code using the new constants.
 */

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * A simple example.
 */
function simple_example() {
  $format = DateTimeItemInterface::DATETIME_STORAGE_FORMAT;
}

/**
 * An example using the constant as an argument.
 */
function as_an_argument() {
  $date = new DrupalDateTime('now', new \DateTimezone('America/Los_Angeles'));
  $now = $date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
}
