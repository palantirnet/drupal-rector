<?php

views_view_is_enabled($view);
views_view_is_disabled($view);
views_enable_view($view);
views_disable_view($view);
$result = views_get_view_result('my_view', 'page_1', 'arg1');

?>
-----
<?php

$view->status();
!$view->status();
$view->enable()->save();
$view->disable()->save();
$result = \Drupal\views\Views::getViewResult('my_view', 'page_1', 'arg1');

?>
