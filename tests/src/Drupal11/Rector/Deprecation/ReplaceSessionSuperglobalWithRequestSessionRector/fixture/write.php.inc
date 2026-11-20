<?php

$_SESSION['my_key'] = $value;
$_SESSION['other_key'] = [$a, $b];
$other_var['key'] = 'untouched';
$_SERVER['x'] = 1;

?>
-----
<?php

\Drupal::request()->getSession()->set('my_key', $value);
\Drupal::request()->getSession()->set('other_key', [$a, $b]);
$other_var['key'] = 'untouched';
$_SERVER['x'] = 1;

?>
