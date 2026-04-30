<?php

$_SESSION['my_key'] = $value;
$_SESSION[$dynamic_key] = $other;
$other_var['key'] = 'untouched';

?>
-----
<?php

\Drupal::request()->getSession()->set('my_key', $value);
\Drupal::request()->getSession()->set($dynamic_key, $other);
$other_var['key'] = 'untouched';

?>
