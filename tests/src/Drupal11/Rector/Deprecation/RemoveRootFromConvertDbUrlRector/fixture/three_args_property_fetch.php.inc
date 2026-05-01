<?php

use Drupal\Core\Database\Database;

// Three-arg call with property fetch: $root removed, bool arg shifts to position 2
Database::convertDbUrlToConnectionInfo($url, $this->root, TRUE);

?>
-----
<?php

use Drupal\Core\Database\Database;

// Three-arg call with property fetch: $root removed, bool arg shifts to position 2
Database::convertDbUrlToConnectionInfo($url, TRUE);

?>
