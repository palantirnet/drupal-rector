<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * An example using the variable table.
 */
function simple_example() {
  db_drop_table('user');
}
