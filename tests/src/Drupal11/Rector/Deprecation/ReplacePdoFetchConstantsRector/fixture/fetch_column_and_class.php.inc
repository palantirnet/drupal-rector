<?php

$statement->setFetchMode(\PDO::FETCH_COLUMN);
$statement->fetch(\PDO::FETCH_CLASS);
$rows = $statement->fetchAll(\PDO::FETCH_COLUMN);

?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $statement->setFetchMode(\Drupal\Core\Database\Statement\FetchAs::Column), fn() => $statement->setFetchMode(\PDO::FETCH_COLUMN));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $statement->fetch(\Drupal\Core\Database\Statement\FetchAs::ClassObject), fn() => $statement->fetch(\PDO::FETCH_CLASS));
$rows = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $statement->fetchAll(\Drupal\Core\Database\Statement\FetchAs::Column), fn() => $statement->fetchAll(\PDO::FETCH_COLUMN));

?>
