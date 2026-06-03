<?php

function example($form, $form_state) {
    _media_library_media_type_form_submit($form, $form_state);
    $result = _media_library_views_form_media_library_after_build($form, $form_state);
}
?>
-----
<?php

function example($form, $form_state) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\media_library\Hook\MediaLibraryHooks::class)->mediaTypeFormSubmit($form, $form_state), fn() => _media_library_media_type_form_submit($form, $form_state));
    $result = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\media_library\Hook\MediaLibraryHooks::class)->viewsFormAfterBuild($form, $form_state), fn() => _media_library_views_form_media_library_after_build($form, $form_state));
}
?>
