<?php

function my_module_store(string $key, mixed $value): mixed {
  $_SESSION[$key] = $value;
  $_SESSION['prefix_' . $key] = $value;
  unset($_SESSION[$key]);
  return $_SESSION[$key] ?? NULL;
}

?>
-----
<?php

function my_module_store(string $key, mixed $value): mixed {
  \Drupal::request()->getSession()->set($key, $value);
  \Drupal::request()->getSession()->set('prefix_' . $key, $value);
  \Drupal::request()->getSession()->remove($key);
  return \Drupal::request()->getSession()->get($key) ?? NULL;
}

?>
