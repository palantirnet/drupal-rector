<?php

// Known limitation: type guard uses ObjectType('SessionManager'), so calls
// on SessionManagerInterface-typed variables are NOT transformed.
/** @var \Drupal\Core\Session\SessionManagerInterface $sessionManager */
$sessionManager->delete($uid);

?>
-----
<?php

// Known limitation: type guard uses ObjectType('SessionManager'), so calls
// on SessionManagerInterface-typed variables are NOT transformed.
/** @var \Drupal\Core\Session\SessionManagerInterface $sessionManager */
$sessionManager->delete($uid);

?>
