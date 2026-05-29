<?php

function example($projects, $langcodes, $source, $project, $template) {
    $result = locale_translation_get_projects(['mymodule']);
    locale_translation_clear_cache_projects();
    $sources = locale_translation_load_sources($projects, $langcodes);
    $built = locale_translation_build_sources($projects, $langcodes);
    locale_translation_source_check_file($source);
    $obj = locale_translation_source_build($project, 'fr', 'mymodule.po');
    $pattern = locale_translation_build_server_pattern($project, $template);
}
?>
-----
<?php

function example($projects, $langcodes, $source, $project, $template) {
    $result = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('locale.project')->getProjects(['mymodule']), fn() => locale_translation_get_projects(['mymodule']));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('locale.project')->resetCache(), fn() => locale_translation_clear_cache_projects());
    $sources = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\locale\LocaleSource')->loadSources($projects, $langcodes), fn() => locale_translation_load_sources($projects, $langcodes));
    $built = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\locale\LocaleSource')->buildSources($projects, $langcodes), fn() => locale_translation_build_sources($projects, $langcodes));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\locale\LocaleSource')->sourceCheckFile($source), fn() => locale_translation_source_check_file($source));
    $obj = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\locale\LocaleSource')->sourceBuild($project, 'fr', 'mymodule.po'), fn() => locale_translation_source_build($project, 'fr', 'mymodule.po'));
    $pattern = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\locale\LocaleSource')->buildServerPattern($project, $template), fn() => locale_translation_build_server_pattern($project, $template));
}
?>
