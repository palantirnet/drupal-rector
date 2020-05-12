<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

use Drupal\Component\FileSystem\FileSystem;

/**
 * A simple example.
 */
function simple_example() {
    $x = FileSystem::getOsTemporaryDirectory();
}
