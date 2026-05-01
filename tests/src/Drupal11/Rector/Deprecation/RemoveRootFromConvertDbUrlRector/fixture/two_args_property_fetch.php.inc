<?php

use Drupal\Core\Database\Database;

// Two-arg call where second arg is a property fetch (no third arg)
Database::convertDbUrlToConnectionInfo($url, $this->root);

?>
-----
<?php

use Drupal\Core\Database\Database;

// Two-arg call where second arg is a property fetch (no third arg)
Database::convertDbUrlToConnectionInfo($url);

?>
