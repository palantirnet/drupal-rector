<?php

function rename_example() {
    $service = \Drupal::service('old.service');
    $other = \Drupal::service('untouched.service');
}
?>
-----
<?php

function rename_example() {
    $service = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('new.service'), fn() => \Drupal::service('old.service'));
    $other = \Drupal::service('untouched.service');
}
?>
