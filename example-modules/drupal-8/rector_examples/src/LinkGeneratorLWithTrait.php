<?php

namespace Drupal\rector_examples;

use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Example of static calls from a class with the trait.
 */
class LinkGeneratorLWithTrait {

  use LinkGeneratorTrait;

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    $url = Url::fromUri('public://');

    $this->l('text', $url);
  }

  /**
   * This shows using a variable as the text.
   */
  public function text_as_variable() {
    $text = 'text';
    $url = Url::fromUri('public://');

    $this->l($text, $url);
  }

}
