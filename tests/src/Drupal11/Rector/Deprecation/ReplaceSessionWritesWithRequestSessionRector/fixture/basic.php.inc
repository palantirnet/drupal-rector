<?php

$_SESSION['my_key'] = $value;
$_SESSION[$dynamic_key] = $other;
$other_var['key'] = 'untouched';

?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::request()->getSession()->set('my_key', $value), fn() => $_SESSION['my_key'] = $value);
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::request()->getSession()->set($dynamic_key, $other), fn() => $_SESSION[$dynamic_key] = $other);
$other_var['key'] = 'untouched';

?>
