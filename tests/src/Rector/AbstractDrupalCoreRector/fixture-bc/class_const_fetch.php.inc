<?php

function simple_example() {
    $value = OldClass::OLD_CONST;
}
?>
-----
<?php

function simple_example() {
    $value = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.0.0', fn() => \NewClass::NEW_CONST, fn() => OldClass::OLD_CONST);
}
?>
