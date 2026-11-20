<?php

$_SESSION['rows'] = $rows;
foreach ($_SESSION['rows'] as &$row) {
    $row['seen'] = TRUE;
}

?>
-----
<?php

$_SESSION['rows'] = $rows;
// TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
// Custom keys in $_SESSION are deprecated in drupal:11.2.0, but this access cannot be converted automatically ($_SESSION is iterated by reference). Convert every access to this key to \Drupal::request()->getSession() get()/set()/remove() by hand, mixing both leaves the value in two places. See https://www.drupal.org/node/3518914
foreach ($_SESSION['rows'] as &$row) {
    $row['seen'] = TRUE;
}

?>
