<?php

function example($form, $form_state) {
    ckeditor5_filter_format_edit_form_submit($form, $form_state);
    _update_ckeditor5_html_filter($form, $form_state);
}
?>
-----
<?php

function example($form, $form_state) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\ckeditor5\Hook\Ckeditor5Hooks')->filterFormatEditFormSubmit($form, $form_state), fn() => ckeditor5_filter_format_edit_form_submit($form, $form_state));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\ckeditor5\Hook\Ckeditor5Hooks')->updateCkeditor5HtmlFilter($form, $form_state), fn() => _update_ckeditor5_html_filter($form, $form_state));
}
?>
