<?php

media_filter_format_edit_form_validate($form, $form_state);
$form['#validate'][] = 'media_filter_format_edit_form_validate';
?>
-----
<?php

\Drupal::service(\Drupal\media\Hook\MediaHooks::class)->formatEditFormValidate($form, $form_state);
$form['#validate'][] = [\Drupal\media\Hook\MediaHooks::class, 'formatEditFormValidate'];
?>
