<?php

// Zero-argument call is transformed too (default $index = 0).
/** @var \Drupal\Core\Database\StatementPrefetchIterator $statement */
$result = $statement->fetchColumn();

?>
-----
<?php

// Zero-argument call is transformed too (default $index = 0).
/** @var \Drupal\Core\Database\StatementPrefetchIterator $statement */
$result = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $statement->fetchField(), fn() => $statement->fetchColumn());

?>
