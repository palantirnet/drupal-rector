<?php

function example($element) {
    language_process_language_select($element);
}
?>
-----
<?php

function example($element) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\language\Hook\LanguageHooks')->processLanguageSelect($element), fn() => language_process_language_select($element));
}
?>
