<?php

$statement->setFetchMode(\PDO::FETCH_COLUMN);
$statement->fetch(\PDO::FETCH_CLASS);
$rows = $statement->fetchAll(\PDO::FETCH_COLUMN);

?>
-----
<?php

$statement->setFetchMode(\PDO::FETCH_COLUMN);
$statement->fetch(\PDO::FETCH_CLASS);
$rows = $statement->fetchAll(\PDO::FETCH_COLUMN);

?>
