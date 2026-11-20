<?php

function example(\PDOStatement $statement, \PDO $connection) {
  $a = $statement->fetchAll(\PDO::FETCH_ASSOC);
  $b = $connection->query('SELECT 1')->fetch(\PDO::FETCH_OBJ);
  $c = $connection->prepare('SELECT 1')->fetchAll(\PDO::FETCH_NUM);
}
