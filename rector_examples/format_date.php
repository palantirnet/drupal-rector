<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  $formatted_date = format_date(123456);
}

/**
 * An example using all of the arguments.
 */
function using_all_arguments() {
  $formatted_date = format_date(time(), 'custom', 'Y M D', 'America/Los_Angeles', 'en-us');
}
