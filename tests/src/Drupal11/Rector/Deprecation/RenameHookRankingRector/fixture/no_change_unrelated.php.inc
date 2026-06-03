<?php

namespace Drupal\mymodule;

// A different "Hook" attribute (NOT Drupal\Core\Hook\Attribute\Hook) that
// happens to share the 'ranking' argument must NOT be rewritten.
use Drupal\mymodule\Attribute\Hook;

class Foo {

  #[Hook('ranking')]
  public function bar(): array {
    return [];
  }

}
