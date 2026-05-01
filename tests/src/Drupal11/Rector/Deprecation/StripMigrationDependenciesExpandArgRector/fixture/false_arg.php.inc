<?php

// FALSE argument is also removed — the arg value doesn't matter, only presence does.
/** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
$deps = $migration->getMigrationDependencies(FALSE);

?>
-----
<?php

// FALSE argument is also removed — the arg value doesn't matter, only presence does.
/** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
$deps = $migration->getMigrationDependencies();

?>
