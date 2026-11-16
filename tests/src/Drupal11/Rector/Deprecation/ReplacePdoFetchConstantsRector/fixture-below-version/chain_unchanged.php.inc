<?php

function example($connection, $query) {
  $rows = $connection->select('node', 'n')
    ->execute()
    ->fetchAll(\PDO::FETCH_ASSOC);
  $safe = $query->execute()?->fetchAll(\PDO::FETCH_ASSOC);
}
