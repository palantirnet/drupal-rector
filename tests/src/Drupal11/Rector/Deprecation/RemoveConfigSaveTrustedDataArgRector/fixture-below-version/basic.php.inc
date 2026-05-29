<?php

/** @var \Drupal\Core\Config\Config $config */
$config->save(TRUE);
$config->save(FALSE);
$config->save();

// Untyped — must not be changed.
$other->save(TRUE);

?>
-----
<?php

/** @var \Drupal\Core\Config\Config $config */
$config->save(TRUE);
$config->save(FALSE);
$config->save();

// Untyped — must not be changed.
$other->save(TRUE);

?>
