<?php

use Drupal\Core\Database\StatementInterface;

function example(StatementInterface $statement): void {
    $statement->setFetchMode(\PDO::FETCH_COLUMN);
    $statement->fetch(\PDO::FETCH_CLASS);
    $rows = $statement->fetchAll(\PDO::FETCH_COLUMN);
}

?>
-----
<?php

use Drupal\Core\Database\StatementInterface;

function example(StatementInterface $statement): void {
    $statement->setFetchMode(\PDO::FETCH_COLUMN);
    $statement->fetch(\PDO::FETCH_CLASS);
    $rows = $statement->fetchAll(\PDO::FETCH_COLUMN);
}

?>
