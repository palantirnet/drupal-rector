<?php

// A property named something other than clientStatement IS transformed.
$result = $this->statement->fetchColumn(0);
$result2 = $this->stmt->fetchColumn(2);

?>
-----
<?php

// A property named something other than clientStatement IS transformed.
$result = $this->statement->fetchField(0);
$result2 = $this->stmt->fetchField(2);

?>
