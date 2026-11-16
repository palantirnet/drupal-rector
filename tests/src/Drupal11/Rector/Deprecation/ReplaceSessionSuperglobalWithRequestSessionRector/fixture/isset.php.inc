<?php

$has = isset($_SESSION['my_key']);
if (!isset($_SESSION['my_key'])) {
    $missing = TRUE;
}
if (isset($_SESSION['a'], $_SESSION['b'], $local)) {
    $all = TRUE;
}
$both = isset($_SESSION['x']) && isset($_SESSION['y']);

?>
-----
<?php

$has = \Drupal::request()->getSession()->get('my_key') !== NULL;
if (!(\Drupal::request()->getSession()->get('my_key') !== NULL)) {
    $missing = TRUE;
}
if (\Drupal::request()->getSession()->get('a') !== NULL && \Drupal::request()->getSession()->get('b') !== NULL && isset($local)) {
    $all = TRUE;
}
$both = \Drupal::request()->getSession()->get('x') !== NULL && \Drupal::request()->getSession()->get('y') !== NULL;

?>
