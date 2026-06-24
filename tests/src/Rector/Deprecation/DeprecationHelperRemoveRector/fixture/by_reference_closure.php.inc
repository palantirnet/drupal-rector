<?php

function example(&$variables)
{
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '9.1.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessHtml($variables);
    }, function () use (&$variables) {
        template_preprocess_html($variables);
    });
}

?>
-----
<?php

function example(&$variables)
{
    \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessHtml($variables);
}

?>
