<?php

/** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
$deps = $migration->getMigrationDependencies(TRUE);
$noop = $migration->getMigrationDependencies();
$untyped = $other->getMigrationDependencies(TRUE);

?>
-----
<?php

/** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
$deps = $migration->getMigrationDependencies();
$noop = $migration->getMigrationDependencies();
$untyped = $other->getMigrationDependencies(TRUE);

?>
