<?php

$statement->setFetchMode(\PDO::FETCH_COLUMN);
$statement->fetch(\PDO::FETCH_CLASS);
$rows = $statement->fetchAll(\PDO::FETCH_COLUMN);

?>
-----
<?php

$statement->setFetchMode(\Drupal\Core\Database\Statement\FetchAs::Column);
$statement->fetch(\Drupal\Core\Database\Statement\FetchAs::ClassObject);
$rows = $statement->fetchAll(\Drupal\Core\Database\Statement\FetchAs::Column);

?>
