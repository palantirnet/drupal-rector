<?php

function basketSid() {
  if (empty($_SESSION['basket_user_sid'])) {
    $_SESSION['basket_user_sid'] = md5(uniqid());
  }
  return $_SESSION['basket_user_sid'];
}

?>
-----
<?php

function basketSid() {
  if (empty(\Drupal::request()->getSession()->get('basket_user_sid'))) {
    \Drupal::request()->getSession()->set('basket_user_sid', md5(uniqid()));
  }
  return \Drupal::request()->getSession()->get('basket_user_sid');
}

?>
