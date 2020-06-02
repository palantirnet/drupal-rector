<?php

use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

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
