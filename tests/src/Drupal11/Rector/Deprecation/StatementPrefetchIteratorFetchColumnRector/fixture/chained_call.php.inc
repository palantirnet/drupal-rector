<?php

// fetchColumn() on an untyped method chain — PHPStan cannot confirm
// StatementPrefetchIterator from the return type of query()/select(), so no change.
$result = $database->query('SELECT name FROM {users}')->fetchColumn();
$id = $connection->select('node', 'n')->fetchColumn(1);
