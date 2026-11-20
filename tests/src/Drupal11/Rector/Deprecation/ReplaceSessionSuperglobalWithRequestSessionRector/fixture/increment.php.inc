<?php

$_SESSION['count']++;
--$_SESSION['remaining'];
$_SESSION['total'] += $amount;
$_SESSION['cache']['hits']++;

?>
-----
<?php

$session_count = \Drupal::request()->getSession()->get('count');
$session_count++;
\Drupal::request()->getSession()->set('count', $session_count);
$session_remaining = \Drupal::request()->getSession()->get('remaining');
--$session_remaining;
\Drupal::request()->getSession()->set('remaining', $session_remaining);
$session_total = \Drupal::request()->getSession()->get('total');
$session_total += $amount;
\Drupal::request()->getSession()->set('total', $session_total);
$session_cache = \Drupal::request()->getSession()->get('cache') ?? [];
$session_cache['hits']++;
\Drupal::request()->getSession()->set('cache', $session_cache);

?>
