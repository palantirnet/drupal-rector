<?php

// No receiver type guard: PDOStatement::fetchAll(PDO::FETCH_ASSOC) is also
// transformed. This is a known limitation — the reactor cannot distinguish
// a native PDOStatement from a Drupal statement without PHPStan type info.
$pdoStatement->fetchAll(\PDO::FETCH_ASSOC);

?>
-----
<?php

// No receiver type guard: PDOStatement::fetchAll(PDO::FETCH_ASSOC) is also
// transformed. This is a known limitation — the reactor cannot distinguish
// a native PDOStatement from a Drupal statement without PHPStan type info.
$pdoStatement->fetchAll(\Drupal\Core\Database\Statement\FetchAs::Associative);

?>
