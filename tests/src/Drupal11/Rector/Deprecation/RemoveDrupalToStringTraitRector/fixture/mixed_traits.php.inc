<?php

namespace Drupal\mymodule;

use Drupal\Component\Utility\ToStringTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class WithMixedTraits {
    use ToStringTrait, StringTranslationTrait;

    public function render() {
        return 'hi';
    }
}
?>
-----
<?php

namespace Drupal\mymodule;

use Drupal\Component\Utility\ToStringTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class WithMixedTraits {
    public function __toString(): string
    {
        return (string) $this->render();
    }
    use StringTranslationTrait;

    public function render() {
        return 'hi';
    }
}
?>
