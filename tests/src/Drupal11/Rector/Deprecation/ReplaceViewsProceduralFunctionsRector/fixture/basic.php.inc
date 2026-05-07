<?php

views_view_is_enabled($view);
views_view_is_disabled($view);
views_enable_view($view);
views_disable_view($view);
$result = views_get_view_result('my_view', 'page_1', 'arg1');

?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $view->status(), fn() => views_view_is_enabled($view));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => !$view->status(), fn() => views_view_is_disabled($view));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $view->enable()->save(), fn() => views_enable_view($view));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $view->disable()->save(), fn() => views_disable_view($view));
$result = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\views\Views::getViewResult('my_view', 'page_1', 'arg1'), fn() => views_get_view_result('my_view', 'page_1', 'arg1'));

?>
