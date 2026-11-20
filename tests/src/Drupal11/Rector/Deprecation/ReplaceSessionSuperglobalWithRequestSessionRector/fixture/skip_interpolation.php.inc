<?php

echo "Hello {$_SESSION['name']}";
$_SESSION['name'] = $name;
$_SESSION['other'] = $other;

?>
-----
<?php

// TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
// Custom keys in $_SESSION are deprecated in drupal:11.2.0, but this access cannot be converted automatically ($_SESSION is used inside string interpolation). Convert every access to this key to \Drupal::request()->getSession() get()/set()/remove() by hand, mixing both leaves the value in two places. See https://www.drupal.org/node/3518914
echo "Hello {$_SESSION['name']}";
$_SESSION['name'] = $name;
\Drupal::request()->getSession()->set('other', $other);

?>
