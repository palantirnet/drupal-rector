<?php

$_SESSION['a'] = 1;
$_SESSION[] = $value;
echo $_SESSION['a'];

?>
-----
<?php

$_SESSION['a'] = 1;
// TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
// Custom keys in $_SESSION are deprecated in drupal:11.2.0, but this access cannot be converted automatically ($_SESSION[] pushes onto the session root). Convert every access to this key to \Drupal::request()->getSession() get()/set()/remove() by hand, mixing both leaves the value in two places. See https://www.drupal.org/node/3518914
$_SESSION[] = $value;
echo $_SESSION['a'];

?>
