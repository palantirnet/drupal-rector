<?php

namespace Drupal\rector_examples;

use Drupal\Core\Link;
use Drupal\Core\Url;
/**
 * Example of static method calls from a class.
 */
class DrupalLStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    Link::fromTextAndUrl('User Login', Url::fromRoute('user.login'));
  }

}
