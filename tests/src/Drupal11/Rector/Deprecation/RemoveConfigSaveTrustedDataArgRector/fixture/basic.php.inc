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
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $config->save(), fn() => $config->save(TRUE));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $config->save(), fn() => $config->save(FALSE));
$config->save();

// Untyped — must not be changed.
$other->save(TRUE);

?>
