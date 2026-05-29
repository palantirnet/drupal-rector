<?php

function example($projects, $langcodes, $options) {
    locale_translation_batch_update_build($projects, $langcodes, $options);
    locale_translation_batch_fetch_build($projects, $langcodes, $options);
}
?>
-----
<?php

function example($projects, $langcodes, $options) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\locale\LocaleFetch')->batchUpdateBuild($projects, $langcodes, $options), fn() => locale_translation_batch_update_build($projects, $langcodes, $options));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\locale\LocaleFetch')->batchFetchBuild($projects, $langcodes, $options), fn() => locale_translation_batch_fetch_build($projects, $langcodes, $options));
}
?>
