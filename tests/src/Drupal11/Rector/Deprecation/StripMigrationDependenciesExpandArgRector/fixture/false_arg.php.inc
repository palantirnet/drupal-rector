<?php

// FALSE argument is also removed — the arg value doesn't matter, only presence does.
/** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
$deps = $migration->getMigrationDependencies(FALSE);

?>
-----
<?php

// FALSE argument is also removed — the arg value doesn't matter, only presence does.
/** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
$deps = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.0.0', fn() => $migration->getMigrationDependencies(), fn() => $migration->getMigrationDependencies(FALSE));

?>
