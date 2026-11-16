<?php

function my_module_remember($step, $value) {
  $_SESSION['wizard']['steps'][$step] = $value;
  $_SESSION['wizard']['log'][] = $step;
  unset($_SESSION['wizard']['steps'][$step]);
}

?>
-----
<?php

function my_module_remember($step, $value) {
  $session_wizard = \Drupal::request()->getSession()->get('wizard') ?? [];
  $session_wizard['steps'][$step] = $value;
  \Drupal::request()->getSession()->set('wizard', $session_wizard);
  $session_wizard = \Drupal::request()->getSession()->get('wizard') ?? [];
  $session_wizard['log'][] = $step;
  \Drupal::request()->getSession()->set('wizard', $session_wizard);
  $session_wizard = \Drupal::request()->getSession()->get('wizard');
  unset($session_wizard['steps'][$step]);
  \Drupal::request()->getSession()->set('wizard', $session_wizard);
}

?>
