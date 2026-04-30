<?php

/** @var \Drupal\Core\Session\UserSession $userSession */
$name = $userSession->name;
$other = $untyped->name;

?>
-----
<?php

/** @var \Drupal\Core\Session\UserSession $userSession */
$name = $userSession->getAccountName();
$other = $untyped->name;

?>
