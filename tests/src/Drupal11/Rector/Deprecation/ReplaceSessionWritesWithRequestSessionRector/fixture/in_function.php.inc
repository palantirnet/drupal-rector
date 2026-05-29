<?php

function my_module_store_data(string $key, mixed $value): void {
    $_SESSION[$key] = $value;
    $_SESSION['prefix_' . $key] = $value;
}

?>
-----
<?php

function my_module_store_data(string $key, mixed $value): void {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::request()->getSession()->set($key, $value), fn() => $_SESSION[$key] = $value);
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::request()->getSession()->set('prefix_' . $key, $value), fn() => $_SESSION['prefix_' . $key] = $value);
}

?>
