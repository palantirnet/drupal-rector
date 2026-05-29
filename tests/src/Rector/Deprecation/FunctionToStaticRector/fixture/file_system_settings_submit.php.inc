<?php

function example($form, $form_state) {
    file_system_settings_submit($form, $form_state);
}
?>
-----
<?php

function example($form, $form_state) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal\file\Hook\FileHooks::settingsSubmit($form, $form_state), fn() => file_system_settings_submit($form, $form_state));
}
?>
