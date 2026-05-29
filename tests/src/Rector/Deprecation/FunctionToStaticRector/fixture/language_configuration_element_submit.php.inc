<?php

function example($form, $form_state) {
    language_configuration_element_submit($form, $form_state);
}
?>
-----
<?php

function example($form, $form_state) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\language\Element\LanguageConfiguration::submit($form, $form_state), fn() => language_configuration_element_submit($form, $form_state));
}
?>
