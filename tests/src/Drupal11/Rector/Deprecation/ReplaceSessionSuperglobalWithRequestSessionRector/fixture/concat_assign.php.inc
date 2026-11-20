<?php

$_SESSION['log'] .= $message.PHP_EOL;
$_SESSION['name'] ??= 'anonymous';

?>
-----
<?php

$session_log = \Drupal::request()->getSession()->get('log');
$session_log .= $message.PHP_EOL;
\Drupal::request()->getSession()->set('log', $session_log);
$session_name = \Drupal::request()->getSession()->get('name');
$session_name ??= 'anonymous';
\Drupal::request()->getSession()->set('name', $session_name);

?>
