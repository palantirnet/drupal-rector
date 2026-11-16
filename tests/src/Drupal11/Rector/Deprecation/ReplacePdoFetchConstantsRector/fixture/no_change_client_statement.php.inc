<?php

function example($connection, $statement) {
  $a = $statement->getClientStatement()->fetchAll(\PDO::FETCH_ASSOC);
  $b = $connection->query('SELECT 1')->getClientStatement()->fetch(\PDO::FETCH_OBJ);
  $c = $connection->getClientConnection()->query('SELECT 1')->fetchAll(\PDO::FETCH_NUM);
  $d = $this->database->getClientConnection()->query('SELECT 1')->fetchAll(\PDO::FETCH_ASSOC);
}
