<?php

$path = \Drupal::config('locale.settings')->get('translation.path');
?>
-----
<?php

$path = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\Core\Site\Settings::get('locale_translation_path', 'public://translations'), fn() => \Drupal::config('locale.settings')->get('translation.path'));
?>
