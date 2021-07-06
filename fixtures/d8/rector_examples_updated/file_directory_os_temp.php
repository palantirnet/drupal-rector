<?php

use Drupal\Component\FileSystem\FileSystem;
/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */
/**
 * A simple example.
 */
function simple_example() {
    $x = FileSystem::getOsTemporaryDirectory();
}
