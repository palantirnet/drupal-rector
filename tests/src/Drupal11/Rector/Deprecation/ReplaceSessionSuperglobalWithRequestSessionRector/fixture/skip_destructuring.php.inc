<?php

[$_SESSION['a'], $_SESSION['b']] = [1, 2];
echo $_SESSION['a'];
$_SESSION['c'] = 3;

?>
-----
<?php

// TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
// Custom keys in $_SESSION are deprecated in drupal:11.2.0, but this access cannot be converted automatically ($_SESSION is a destructuring target). Convert every access to this key to \Drupal::request()->getSession() get()/set()/remove() by hand, mixing both leaves the value in two places. See https://www.drupal.org/node/3518914
[$_SESSION['a'], $_SESSION['b']] = [1, 2];
echo $_SESSION['a'];
\Drupal::request()->getSession()->set('c', 3);

?>
