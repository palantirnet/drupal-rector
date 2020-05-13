<?php

namespace Drupal\rector_examples;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Routing\LinkGeneratorTrait;

/**
 * Example of static calls from a class with the trait.
 */
class ControllerBaseLWithTrait {

  use LinkGeneratorTrait;

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    $url = Url::fromUri('public://');

    Link::fromTextAndUrl('text', $url);
  }

  /**
   * This shows using a variable as the text.
   */
  public function text_as_variable() {
    $text = 'text';
    $url = Url::fromUri('public://');

    Link::fromTextAndUrl($text, $url);
  }

}
