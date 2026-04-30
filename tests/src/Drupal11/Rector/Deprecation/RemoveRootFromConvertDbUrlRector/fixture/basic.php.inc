<?php

use Drupal\Core\Database\Database;

// With three args: root removed, bool arg shifts left
Database::convertDbUrlToConnectionInfo($url, $this->root, TRUE);
// With string root, no third arg
Database::convertDbUrlToConnectionInfo($url, $root);
// Already adapted (boolean second arg): leave unchanged
Database::convertDbUrlToConnectionInfo($url, TRUE);

?>
-----
<?php

use Drupal\Core\Database\Database;

// With three args: root removed, bool arg shifts left
Database::convertDbUrlToConnectionInfo($url, TRUE);
// With string root, no third arg
Database::convertDbUrlToConnectionInfo($url, $root);
// Already adapted (boolean second arg): leave unchanged
Database::convertDbUrlToConnectionInfo($url, TRUE);

?>
