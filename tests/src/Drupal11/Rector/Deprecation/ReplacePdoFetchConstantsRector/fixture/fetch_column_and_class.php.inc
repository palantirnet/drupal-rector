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
    $statement->setFetchMode(\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\Core\Database\Statement\FetchAs::Column, fn() => \PDO::FETCH_COLUMN));
    $statement->fetch(\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\Core\Database\Statement\FetchAs::ClassObject, fn() => \PDO::FETCH_CLASS));
    $rows = $statement->fetchAll(\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\Core\Database\Statement\FetchAs::Column, fn() => \PDO::FETCH_COLUMN));
}

?>
