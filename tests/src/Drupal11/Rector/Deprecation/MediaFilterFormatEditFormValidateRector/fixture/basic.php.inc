<?php

media_filter_format_edit_form_validate($form, $form_state);
$form['#validate'][] = 'media_filter_format_edit_form_validate';
?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\media\Hook\MediaHooks::class)->formatEditFormValidate($form, $form_state), fn() => media_filter_format_edit_form_validate($form, $form_state));
$form['#validate'][] = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => [\Drupal\media\Hook\MediaHooks::class, 'formatEditFormValidate'], fn() => 'media_filter_format_edit_form_validate');
?>
