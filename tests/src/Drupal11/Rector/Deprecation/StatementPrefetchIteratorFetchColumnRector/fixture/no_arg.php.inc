<?php

// Zero-argument call is transformed too (default $index = 0).
/** @var \Drupal\Core\Database\StatementPrefetchIterator $statement */
$result = $statement->fetchColumn();

?>
-----
<?php

// Zero-argument call is transformed too (default $index = 0).
/** @var \Drupal\Core\Database\StatementPrefetchIterator $statement */
$result = $statement->fetchField();

?>
