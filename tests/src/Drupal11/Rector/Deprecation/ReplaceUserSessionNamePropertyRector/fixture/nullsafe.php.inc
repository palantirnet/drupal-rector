<?php

// NullsafePropertyFetch ($session?->name) is a different node type from
// PropertyFetch — not handled by this rector.
/** @var \Drupal\Core\Session\UserSession|null $session */
$name = $session?->name;

?>
-----
<?php

// NullsafePropertyFetch ($session?->name) is a different node type from
// PropertyFetch — not handled by this rector.
/** @var \Drupal\Core\Session\UserSession|null $session */
$name = $session?->name;

?>
