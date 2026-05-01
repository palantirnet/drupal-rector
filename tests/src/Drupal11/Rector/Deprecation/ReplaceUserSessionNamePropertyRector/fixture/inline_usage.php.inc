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
if ($session->getAccountName() === 'admin') {
    return $session->getAccountName();
}
$label = 'Hello ' . $session->getAccountName();

?>
