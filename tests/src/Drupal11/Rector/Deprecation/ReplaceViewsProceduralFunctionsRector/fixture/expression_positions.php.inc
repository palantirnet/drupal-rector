<?php

if (views_view_is_enabled($view)) {
    $label = views_view_is_disabled($view) ? 'disabled' : 'enabled';
}
$result = views_get_view_result('frontpage');
$result2 = views_get_view_result('archive', 'block_1');

?>
-----
<?php

if ($view->status()) {
    $label = !$view->status() ? 'disabled' : 'enabled';
}
$result = \Drupal\views\Views::getViewResult('frontpage');
$result2 = \Drupal\views\Views::getViewResult('archive', 'block_1');

?>
