<?php

$name = $_SESSION['name'] ?? 'default';
$deep = $_SESSION['config']['theme'] ?? $fallback;

?>
-----
<?php

$name = \Drupal::request()->getSession()->get('name') ?? 'default';
$deep = \Drupal::request()->getSession()->get('config')['theme'] ?? $fallback;

?>
