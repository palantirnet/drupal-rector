<?php

// Zero-argument call is transformed too (default $index = 0).
$result = $statement->fetchColumn();

?>
-----
<?php

// Zero-argument call is transformed too (default $index = 0).
$result = $statement->fetchField();

?>
