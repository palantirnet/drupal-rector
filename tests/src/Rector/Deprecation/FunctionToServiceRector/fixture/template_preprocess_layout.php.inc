<?php

function my_preprocess(&$variables) {
    template_preprocess_layout($variables);
}
?>
-----
<?php

function my_preprocess(&$variables) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', function () use (&$variables) {
        \Drupal::service(\Drupal\layout_discovery\Hook\LayoutDiscoveryThemeHooks::class)->preprocessLayout($variables);
    }, function () use (&$variables) {
        template_preprocess_layout($variables);
    });
}
?>
