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

$statement->setFetchMode(\PDO::FETCH_ASSOC);
$statement->fetch(\PDO::FETCH_OBJ);
$rows = $statement->fetchAll(\PDO::FETCH_NUM);
$data = $statement->fetchAllAssoc('id', \PDO::FETCH_ASSOC);
$result = $db->query('SELECT name FROM {test}', [], ['fetch' => \PDO::FETCH_ASSOC]);
// Raw PDO: leave unchanged
$pdo = $statement->getClientStatement()->fetchAll(\PDO::FETCH_ASSOC);

?>
