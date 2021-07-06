<?php

namespace Drupal\rector_examples;

use Drupal\Core\Link;
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

    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // Please manually remove the `use LinkGeneratorTrait;` statement from this class.
    Link::fromTextAndUrl('text', $url);
  }

  /**
   * This shows using a variable as the text.
   */
  public function text_as_variable() {
    $text = 'text';
    $url = Url::fromUri('public://');

    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // Please manually remove the `use LinkGeneratorTrait;` statement from this class.
    Link::fromTextAndUrl($text, $url);
  }

}
