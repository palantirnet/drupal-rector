<?php

use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  $timezone = DateTimeItemInterface::STORAGE_TIMEZONE;
}

/**
 * An example using the constant as an argument.
 */
function as_an_argument() {
  $timezone = new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE);
}
