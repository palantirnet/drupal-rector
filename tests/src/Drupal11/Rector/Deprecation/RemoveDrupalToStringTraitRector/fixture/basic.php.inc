<?php

namespace Drupal\mymodule;

use Drupal\Component\Utility\ToStringTrait;

class MyMarkup {
    use ToStringTrait;

    public function render() {
        return 'hello';
    }
}
?>
-----
<?php

namespace Drupal\mymodule;

use Drupal\Component\Utility\ToStringTrait;

class MyMarkup {
    public function __toString(): string
    {
        return (string) $this->render();
    }

    public function render() {
        return 'hello';
    }
}
?>
