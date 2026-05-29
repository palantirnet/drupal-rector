<?php

/** @var \Drupal\Core\Session\UserSession $session */
if ($session->name === 'admin') {
    return $session->name;
}
$label = 'Hello ' . $session->name;

?>
-----
<?php

/** @var \Drupal\Core\Session\UserSession $session */
if (\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $session->getAccountName(), fn() => $session->name) === 'admin') {
    return \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $session->getAccountName(), fn() => $session->name);
}
$label = 'Hello ' . \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $session->getAccountName(), fn() => $session->name);

?>
