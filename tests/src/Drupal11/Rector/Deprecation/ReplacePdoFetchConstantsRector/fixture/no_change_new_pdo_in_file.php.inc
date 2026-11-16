<?php

// Upgrade Status skips files that instantiate PDO: these are genuine PDO calls.
function example($connection) {
  $pdo = new \PDO('sqlite::memory:');
  $stmt = $pdo->query('SELECT 1');
  $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
  $other = $connection->select('node', 'n')->execute()->fetchAll(\PDO::FETCH_ASSOC);
}
