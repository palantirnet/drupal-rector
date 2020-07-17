<?php

use Drupal\Core\Link;
use Drupal\Core\Url;
/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */
/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
    Link::fromTextAndUrl('User Login', Url::fromRoute('user.login'));
}
