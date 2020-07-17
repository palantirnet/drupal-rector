<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  $x = FILE_EXISTS_RENAME;
}

/**
 * An example using the constant as an argument.
 */
function as_an_argument() {
  file_unmanaged_copy('/test/directory', '/test/directory/new' . '/file_name.json', FILE_EXISTS_RENAME);
}
