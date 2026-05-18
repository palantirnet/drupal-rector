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

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $statement->setFetchMode(\Drupal\Core\Database\Statement\FetchAs::Associative), fn() => $statement->setFetchMode(\PDO::FETCH_ASSOC));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $statement->fetch(\Drupal\Core\Database\Statement\FetchAs::Object), fn() => $statement->fetch(\PDO::FETCH_OBJ));
$rows = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $statement->fetchAll(\Drupal\Core\Database\Statement\FetchAs::List), fn() => $statement->fetchAll(\PDO::FETCH_NUM));
$data = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $statement->fetchAllAssoc('id', \Drupal\Core\Database\Statement\FetchAs::Associative), fn() => $statement->fetchAllAssoc('id', \PDO::FETCH_ASSOC));
$result = $db->query('SELECT name FROM {test}', [], ['fetch' => \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\Core\Database\Statement\FetchAs::Associative, fn() => \PDO::FETCH_ASSOC)]);
// Raw PDO: leave unchanged
$pdo = $statement->getClientStatement()->fetchAll(\PDO::FETCH_ASSOC);

?>
