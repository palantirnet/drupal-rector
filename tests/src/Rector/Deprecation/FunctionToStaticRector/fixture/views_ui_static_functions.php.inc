<?php

function example($element, $form_state, $form, $section) {
    views_ui_form_button_was_clicked($element, $form_state);
    views_ui_add_limited_validation($element, $form_state);
    views_ui_add_ajax_wrapper($element, $form_state);
    views_ui_nojs_submit($form, $form_state);
}
?>
-----
<?php

function example($element, $form_state, $form, $section) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\views\ViewsFormHelperTrait::formButtonWasClicked($element, $form_state), fn() => views_ui_form_button_was_clicked($element, $form_state));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\views\ViewsFormAjaxHelperTrait::addLimitedValidation($element, $form_state), fn() => views_ui_add_limited_validation($element, $form_state));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\views\ViewsFormAjaxHelperTrait::addAjaxWrapper($element, $form_state), fn() => views_ui_add_ajax_wrapper($element, $form_state));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\views\ViewsFormAjaxHelperTrait::noJsSubmit($form, $form_state), fn() => views_ui_nojs_submit($form, $form_state));
}
?>
