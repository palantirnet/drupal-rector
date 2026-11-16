<?php

class Example {

  public $database;

  public function run($connection, $query, $uid) {
    $rows = $connection->select('achievement_unlocks', 'au')
      ->fields('au', ['achievement_id', 'rank'])
      ->condition('uid', $uid)
      ->execute()
      ->fetchAllAssoc('achievement_id', \PDO::FETCH_ASSOC);

    $paged = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')
      ->execute()
      ->fetchAll(\PDO::FETCH_ASSOC);

    $untyped = $this->database->select('node', 'n')
      ->fields('n')
      ->execute()
      ->fetchAll(\PDO::FETCH_OBJ);

    if ($result = $query->execute()) {
      $all = $result->fetchAll(\PDO::FETCH_NUM);
    }

    $safe = $query->execute()?->fetchAll(\PDO::FETCH_ASSOC);
    $col = $this->xConnection->query('SELECT 1')->fetch(\PDO::FETCH_COLUMN);
    $one = $this->xConnection->query('SELECT 1')->setFetchMode(\PDO::FETCH_CLASS);
  }

}
?>
-----
<?php

class Example {

  public $database;

  public function run($connection, $query, $uid) {
    $rows = $connection->select('achievement_unlocks', 'au')
      ->fields('au', ['achievement_id', 'rank'])
      ->condition('uid', $uid)
      ->execute()
      ->fetchAllAssoc('achievement_id', \Drupal\Core\Database\Statement\FetchAs::Associative);

    $paged = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')
      ->execute()
      ->fetchAll(\Drupal\Core\Database\Statement\FetchAs::Associative);

    $untyped = $this->database->select('node', 'n')
      ->fields('n')
      ->execute()
      ->fetchAll(\Drupal\Core\Database\Statement\FetchAs::Object);

    if ($result = $query->execute()) {
      $all = $result->fetchAll(\Drupal\Core\Database\Statement\FetchAs::List);
    }

    $safe = $query->execute()?->fetchAll(\Drupal\Core\Database\Statement\FetchAs::Associative);
    $col = $this->xConnection->query('SELECT 1')->fetch(\Drupal\Core\Database\Statement\FetchAs::Column);
    $one = $this->xConnection->query('SELECT 1')->setFetchMode(\Drupal\Core\Database\Statement\FetchAs::ClassObject);
  }

}
?>
