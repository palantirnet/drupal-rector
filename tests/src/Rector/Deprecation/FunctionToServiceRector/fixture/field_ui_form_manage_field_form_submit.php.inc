<?php

field_ui_form_manage_field_form_submit($form, $form_state);
?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\field_ui\Hook\FieldUiHooks::class)->manageFieldFormSubmit($form, $form_state), fn() => field_ui_form_manage_field_form_submit($form, $form_state));
?>
