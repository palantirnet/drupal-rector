<?php

/** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
$deps = $migration->getMigrationDependencies(TRUE);
$noop = $migration->getMigrationDependencies();
$untyped = $other->getMigrationDependencies(TRUE);

?>
-----
<?php

/** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
$deps = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.0.0', fn() => $migration->getMigrationDependencies(), fn() => $migration->getMigrationDependencies(TRUE));
$noop = $migration->getMigrationDependencies();
$untyped = $other->getMigrationDependencies(TRUE);

?>
