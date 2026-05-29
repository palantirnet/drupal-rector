<?php

/** @var \Drupal\Core\Session\UserSession $userSession */
$name = $userSession->name;
$other = $untyped->name;

?>
-----
<?php

/** @var \Drupal\Core\Session\UserSession $userSession */
$name = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $userSession->getAccountName(), fn() => $userSession->name);
$other = $untyped->name;

?>
