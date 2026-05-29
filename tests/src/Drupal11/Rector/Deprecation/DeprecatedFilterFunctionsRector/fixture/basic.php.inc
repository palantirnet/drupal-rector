<?php

$result1 = _filter_autop($text);
$result2 = _filter_html_escape($text);
$result3 = _filter_html_image_secure_process($text, $filter);

?>
-----
<?php

$result1 = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('plugin.manager.filter')->createInstance('filter_autop')->process($text, \Drupal::languageManager()->getCurrentLanguage()->getId())->getProcessedText(), fn() => _filter_autop($text));
$result2 = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('plugin.manager.filter')->createInstance('filter_html_escape')->process($text, \Drupal::languageManager()->getCurrentLanguage()->getId())->getProcessedText(), fn() => _filter_html_escape($text));
$result3 = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('plugin.manager.filter')->createInstance('filter_html_image_secure')->process($text, \Drupal::languageManager()->getCurrentLanguage()->getId())->getProcessedText(), fn() => _filter_html_image_secure_process($text, $filter));

?>
