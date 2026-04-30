<?php

function example() {
    language_configuration_element_submit($form, $form_state);
    language_process_language_select($element);
}
?>
-----
<?php

function example() {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\language\Element\LanguageConfiguration::submit($form, $form_state), fn() => language_configuration_element_submit($form, $form_state));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\language\Hook\LanguageHooks::class)->processLanguageSelect($element), fn() => language_process_language_select($element));
}
?>
