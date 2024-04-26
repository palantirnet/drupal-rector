<?php

function simple_example() {
    _drupal_flush_css_js();
}
?>
-----
<?php

function simple_example() {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.2.0', fn() => \Drupal::service('asset.query_string')->reset(), fn() => _drupal_flush_css_js());
}
?>
