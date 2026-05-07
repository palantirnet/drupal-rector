<?php

if (views_view_is_enabled($view)) {
    $label = views_view_is_disabled($view) ? 'disabled' : 'enabled';
}
$result = views_get_view_result('frontpage');
$result2 = views_get_view_result('archive', 'block_1');

?>
-----
<?php

if (\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $view->status(), fn() => views_view_is_enabled($view))) {
    $label = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => !$view->status(), fn() => views_view_is_disabled($view)) ? 'disabled' : 'enabled';
}
$result = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\views\Views::getViewResult('frontpage'), fn() => views_get_view_result('frontpage'));
$result2 = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\views\Views::getViewResult('archive', 'block_1'), fn() => views_get_view_result('archive', 'block_1'));

?>
