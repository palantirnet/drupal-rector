<?php

locale_translation_batch_version_check($project, $langcode, $context);
locale_translation_batch_status_check($project, $langcode, $options, $context);
locale_translation_batch_fetch_download($project, $langcode, $context);
locale_translation_batch_fetch_import($project, $langcode, $options, $context);
locale_translation_batch_fetch_finished($success, $results);
_locale_translation_batch_status_operations($projects, $langcodes, $options);
locale_translate_batch_build($files, $options);
locale_translate_batch_import($file, $options, $context);
locale_translate_batch_import_save($context);
locale_translate_batch_refresh($context);
locale_translate_batch_finished($success, $results);
locale_config_batch_update_components($options, $langcodes, $components, $update_default_config_langcodes);
locale_config_batch_update_default_config_langcodes($context);
locale_config_batch_update_config_translations($names, $langcodes, $context);
locale_config_batch_finished($success, $results);
locale_translation_check_projects_batch($projects, $langcodes);
locale_translation_batch_status_finished($success, $results);
?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleFetch::class)->batchVersionCheck($project, $langcode, $context), fn() => locale_translation_batch_version_check($project, $langcode, $context));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleFetch::class)->batchStatusCheck($project, $langcode, $options, $context), fn() => locale_translation_batch_status_check($project, $langcode, $options, $context));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleFetch::class)->batchDownload($project, $langcode, $context), fn() => locale_translation_batch_fetch_download($project, $langcode, $context));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleFetch::class)->batchImport($project, $langcode, $options, $context), fn() => locale_translation_batch_fetch_import($project, $langcode, $options, $context));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleFetch::class)->batchFinished($success, $results), fn() => locale_translation_batch_fetch_finished($success, $results));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleFetch::class)->getStatusOperations($projects, $langcodes, $options), fn() => _locale_translation_batch_status_operations($projects, $langcodes, $options));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleImportBatch::class)->buildBatch($files, $options), fn() => locale_translate_batch_build($files, $options));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleImportBatch::class)->batchImport($file, $options, $context), fn() => locale_translate_batch_import($file, $options, $context));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleImportBatch::class)->batchSave($context), fn() => locale_translate_batch_import_save($context));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleImportBatch::class)->batchRefresh($context), fn() => locale_translate_batch_refresh($context));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleImportBatch::class)->batchFinished($success, $results), fn() => locale_translate_batch_finished($success, $results));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleConfigBatch::class)->buildBatch($options, $langcodes, $components, $update_default_config_langcodes), fn() => locale_config_batch_update_components($options, $langcodes, $components, $update_default_config_langcodes));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleConfigBatch::class)->batchUpdateDefaultConfigLangcodes($context), fn() => locale_config_batch_update_default_config_langcodes($context));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleConfigBatch::class)->batchUpdateConfigTranslations($names, $langcodes, $context), fn() => locale_config_batch_update_config_translations($names, $langcodes, $context));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleConfigBatch::class)->batchFinished($success, $results), fn() => locale_config_batch_finished($success, $results));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleProjectChecker::class)->triggerBatch($projects, $langcodes), fn() => locale_translation_check_projects_batch($projects, $langcodes));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleProjectChecker::class)->batchFinished($success, $results), fn() => locale_translation_batch_status_finished($success, $results));
?>
