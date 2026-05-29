<?php

use Drupal\Core\Database\Database;

// Second arg is already a bool: already adapted form, leave unchanged
Database::convertDbUrlToConnectionInfo($url, TRUE);
Database::convertDbUrlToConnectionInfo($url, FALSE);

?>
-----
<?php

use Drupal\Core\Database\Database;

// Second arg is already a bool: already adapted form, leave unchanged
Database::convertDbUrlToConnectionInfo($url, TRUE);
Database::convertDbUrlToConnectionInfo($url, FALSE);

?>
