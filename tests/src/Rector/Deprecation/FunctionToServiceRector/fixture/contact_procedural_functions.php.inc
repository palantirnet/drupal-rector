<?php

function example($form, $form_state) {
    contact_user_profile_form_submit($form, $form_state);
    contact_form_user_admin_settings_submit($form, $form_state);
}
?>
-----
<?php

function example($form, $form_state) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\contact\Hook\ContactFormHooks')->profileFormSubmit($form, $form_state), fn() => contact_user_profile_form_submit($form, $form_state));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\contact\Hook\ContactFormHooks')->userAdminSettingsSubmit($form, $form_state), fn() => contact_form_user_admin_settings_submit($form, $form_state));
}
?>
