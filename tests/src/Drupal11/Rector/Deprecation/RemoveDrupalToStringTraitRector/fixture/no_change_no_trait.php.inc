<?php

namespace Drupal\mymodule;

use Drupal\Core\StringTranslation\StringTranslationTrait;

class UnrelatedClass {
    use StringTranslationTrait;

    public function render() {
        return 'hi';
    }
}
