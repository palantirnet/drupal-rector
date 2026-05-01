<?php

use Drupal\Core\Database\Database;

// Second arg is a method call result (string-returning method): remove it
Database::convertDbUrlToConnectionInfo($url, $this->getRoot());
Database::convertDbUrlToConnectionInfo($url, $this->getDrupalRoot(), TRUE);

?>
-----
<?php

use Drupal\Core\Database\Database;

// Second arg is a method call result (string-returning method): remove it
Database::convertDbUrlToConnectionInfo($url);
Database::convertDbUrlToConnectionInfo($url, TRUE);

?>
