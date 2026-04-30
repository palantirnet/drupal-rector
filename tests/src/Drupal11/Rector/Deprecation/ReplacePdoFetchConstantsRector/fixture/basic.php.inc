<?php

$statement->setFetchMode(\PDO::FETCH_ASSOC);
$statement->fetch(\PDO::FETCH_OBJ);
$rows = $statement->fetchAll(\PDO::FETCH_NUM);
$data = $statement->fetchAllAssoc('id', \PDO::FETCH_ASSOC);
$result = $db->query('SELECT name FROM {test}', [], ['fetch' => \PDO::FETCH_ASSOC]);
// Raw PDO: leave unchanged
$pdo = $statement->getClientStatement()->fetchAll(\PDO::FETCH_ASSOC);

?>
-----
<?php

$statement->setFetchMode(\Drupal\Core\Database\Statement\FetchAs::Associative);
$statement->fetch(\Drupal\Core\Database\Statement\FetchAs::Object);
$rows = $statement->fetchAll(\Drupal\Core\Database\Statement\FetchAs::List);
$data = $statement->fetchAllAssoc('id', \Drupal\Core\Database\Statement\FetchAs::Associative);
$result = $db->query('SELECT name FROM {test}', [], ['fetch' => \Drupal\Core\Database\Statement\FetchAs::Associative]);
// Raw PDO: leave unchanged
$pdo = $statement->getClientStatement()->fetchAll(\PDO::FETCH_ASSOC);

?>
