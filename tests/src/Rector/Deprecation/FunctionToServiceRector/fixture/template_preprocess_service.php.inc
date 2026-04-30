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
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessContainer($variables), fn() => template_preprocess_container($variables));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessLinks($variables), fn() => template_preprocess_links($variables));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessHtml($variables), fn() => template_preprocess_html($variables));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessPage($variables), fn() => template_preprocess_page($variables));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::service('Drupal\Core\Datetime\DatePreprocess')->preprocessTime($variables), fn() => template_preprocess_time($variables));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::service('Drupal\Core\Datetime\DatePreprocess')->preprocessDatetimeForm($variables), fn() => template_preprocess_datetime_form($variables));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::service('Drupal\Core\Datetime\DatePreprocess')->preprocessDatetimeWrapper($variables), fn() => template_preprocess_datetime_wrapper($variables));
}
?>
