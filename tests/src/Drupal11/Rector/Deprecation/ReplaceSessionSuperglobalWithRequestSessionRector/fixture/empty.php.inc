<?php

if (empty($_SESSION['my_key'])) {
    $_SESSION['my_key'] = $default;
}
$blank = !empty($_SESSION['other']);

?>
-----
<?php

if (empty(\Drupal::request()->getSession()->get('my_key'))) {
    \Drupal::request()->getSession()->set('my_key', $default);
}
$blank = !empty(\Drupal::request()->getSession()->get('other'));

?>
