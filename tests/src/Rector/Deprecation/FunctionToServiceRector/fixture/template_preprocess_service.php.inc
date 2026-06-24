<?php

function example(&$variables) {
    template_preprocess_container($variables);
    template_preprocess_links($variables);
    template_preprocess_html($variables);
    template_preprocess_page($variables);
    template_preprocess_time($variables);
    template_preprocess_datetime_form($variables);
    template_preprocess_datetime_wrapper($variables);
}
?>
-----
<?php

function example(&$variables) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessContainer($variables);
    }, function () use (&$variables) {
        template_preprocess_container($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessLinks($variables);
    }, function () use (&$variables) {
        template_preprocess_links($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessHtml($variables);
    }, function () use (&$variables) {
        template_preprocess_html($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessPage($variables);
    }, function () use (&$variables) {
        template_preprocess_page($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Datetime\DatePreprocess')->preprocessTime($variables);
    }, function () use (&$variables) {
        template_preprocess_time($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Datetime\DatePreprocess')->preprocessDatetimeForm($variables);
    }, function () use (&$variables) {
        template_preprocess_datetime_form($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Datetime\DatePreprocess')->preprocessDatetimeWrapper($variables);
    }, function () use (&$variables) {
        template_preprocess_datetime_wrapper($variables);
    });
}
?>
