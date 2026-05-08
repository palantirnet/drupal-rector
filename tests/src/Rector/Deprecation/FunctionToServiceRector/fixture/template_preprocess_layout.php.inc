<?php

function my_preprocess(&$variables) {
    template_preprocess_layout($variables);
}
?>
-----
<?php

function my_preprocess(&$variables) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service('Drupal\layout_discovery\Hook\LayoutDiscoveryThemeHooks')->preprocessLayout($variables), fn() => template_preprocess_layout($variables));
}
?>
