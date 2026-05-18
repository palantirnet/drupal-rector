<?php

locale_translation_flush_projects();
locale_translation_build_projects();
locale_translation_check_projects();
locale_translation_check_projects(['drupal'], ['de']);
locale_translation_check_projects_local(['drupal'], ['de']);
?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleProjectRepository::class)->deleteAll(), fn() => locale_translation_flush_projects());
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleProjectRepository::class)->buildProjects(), fn() => locale_translation_build_projects());
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleProjectChecker::class)->checkProjects(array_keys(\Drupal::service(\Drupal\locale\LocaleProjectRepository::class)->getAll())), fn() => locale_translation_check_projects());
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleProjectChecker::class)->checkProjects(['drupal'], ['de']), fn() => locale_translation_check_projects(['drupal'], ['de']));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\locale\LocaleProjectChecker::class)->checkLocalProjects(['drupal'], ['de']), fn() => locale_translation_check_projects_local(['drupal'], ['de']));
?>
