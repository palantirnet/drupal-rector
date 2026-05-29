<?php

function example($type) {
    _media_library_configure_form_display($type);
    _media_library_configure_view_display($type);
}
?>
-----
<?php

function example($type) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\media_library\MediaLibraryDisplayManager::configureFormDisplay($type), fn() => _media_library_configure_form_display($type));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\media_library\MediaLibraryDisplayManager::configureViewDisplay($type), fn() => _media_library_configure_view_display($type));
}
?>
