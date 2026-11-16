<?php

$theme = $_SESSION['config']['theme'];
$has = isset($_SESSION['config']['theme']);
$blank = empty($_SESSION['config']['theme']['name']);

?>
-----
<?php

$theme = \Drupal::request()->getSession()->get('config')['theme'];
$has = isset(\Drupal::request()->getSession()->get('config')['theme']);
$blank = empty(\Drupal::request()->getSession()->get('config')['theme']['name']);

?>
