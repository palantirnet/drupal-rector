<?php

use Drupal\Core\Database\StatementInterface;

// FETCH_GROUP and friends have no FetchAs case, and non-fetch methods are not touched.
function example(StatementInterface $statement, $untyped, $connection) {
  $a = $statement->fetchAll(\PDO::FETCH_GROUP);
  $b = $statement->fetchAll(\PDO::FETCH_COLUMN | \PDO::FETCH_GROUP);
  $c = $untyped->select('node', 'n')->execute()->fetchAll(\PDO::FETCH_KEY_PAIR);
  $d = $connection->select('node', 'n')->execute()->fetchCol(\PDO::FETCH_ASSOC);
  $e = $connection->unknownMethod(\PDO::FETCH_ASSOC);
}
