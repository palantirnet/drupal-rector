<?php

use Drupal\Core\Database\Database;

// Second arg is a plain variable: cannot statically determine type, leave unchanged
Database::convertDbUrlToConnectionInfo($url, $root);

?>
-----
<?php

use Drupal\Core\Database\Database;

// Second arg is a plain variable: cannot statically determine type, leave unchanged
Database::convertDbUrlToConnectionInfo($url, $root);

?>
