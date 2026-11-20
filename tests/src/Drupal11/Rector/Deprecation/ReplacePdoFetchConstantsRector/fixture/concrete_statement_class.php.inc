<?php

function example($query) {
  /** @var \Drupal\Core\Database\Statement $result */
  $result = $query->execute();
  $result->setFetchMode(\PDO::FETCH_CLASS, ResultRow::class);
}
?>
-----
<?php

function example($query) {
  /** @var \Drupal\Core\Database\Statement $result */
  $result = $query->execute();
  $result->setFetchMode(\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\Core\Database\Statement\FetchAs::ClassObject, fn() => \PDO::FETCH_CLASS), ResultRow::class);
}
?>
