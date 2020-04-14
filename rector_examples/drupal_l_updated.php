<?php

/**
 * This demonstrates the updated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
    \Drupal::service('link_generator')->generate('User Login', \Drupal::service('url_generator')->generateFromRoute('user.login'));
}

