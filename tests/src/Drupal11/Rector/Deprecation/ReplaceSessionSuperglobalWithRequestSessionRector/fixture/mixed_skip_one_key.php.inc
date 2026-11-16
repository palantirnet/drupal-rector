<?php

$_SESSION['converted'] = $value;
echo $_SESSION['converted'];
$_SESSION['skipped'] = $value;
$alias = &$_SESSION['skipped'];
echo isset($_SESSION['skipped']);

?>
-----
<?php

\Drupal::request()->getSession()->set('converted', $value);
echo \Drupal::request()->getSession()->get('converted');
$_SESSION['skipped'] = $value;
// TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
// Custom keys in $_SESSION are deprecated in drupal:11.2.0, but this access cannot be converted automatically ($_SESSION is aliased by reference). Convert every access to this key to \Drupal::request()->getSession() get()/set()/remove() by hand, mixing both leaves the value in two places. See https://www.drupal.org/node/3518914
$alias = &$_SESSION['skipped'];
echo isset($_SESSION['skipped']);

?>
