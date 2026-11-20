<?php

$_SESSION['a'] = 1;
$result = $_SESSION['token'] = generate();
echo $_SESSION['token'];

?>
-----
<?php

\Drupal::request()->getSession()->set('a', 1);
// TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
// Custom keys in $_SESSION are deprecated in drupal:11.2.0, but this access cannot be converted automatically (an assignment to $_SESSION is used as a value). Convert every access to this key to \Drupal::request()->getSession() get()/set()/remove() by hand, mixing both leaves the value in two places. See https://www.drupal.org/node/3518914
$result = $_SESSION['token'] = generate();
echo $_SESSION['token'];

?>
