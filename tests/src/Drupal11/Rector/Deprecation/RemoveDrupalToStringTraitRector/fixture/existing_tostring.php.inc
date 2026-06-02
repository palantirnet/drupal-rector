<?php

namespace Drupal\mymodule;

use Drupal\Component\Utility\ToStringTrait;

class HasOwnToString {
    use ToStringTrait;

    public function __toString(): string {
        return 'custom';
    }

    public function render() {
        return 'ignored';
    }
}
?>
-----
<?php

namespace Drupal\mymodule;

use Drupal\Component\Utility\ToStringTrait;

class HasOwnToString {
    public function __toString(): string {
        return 'custom';
    }

    public function render() {
        return 'ignored';
    }
}
?>
