<?php

// fetchColumn() on the result of a method call chain.
$result = $database->query('SELECT name FROM {users}')->fetchColumn();
$id = $connection->select('node', 'n')->fetchColumn(1);

?>
-----
<?php

// fetchColumn() on the result of a method call chain.
$result = $database->query('SELECT name FROM {users}')->fetchField();
$id = $connection->select('node', 'n')->fetchField(1);

?>
