<?php

use Drupal\Core\Database\Database;

// Two-arg call with string literal root: removed, leaving only $url
Database::convertDbUrlToConnectionInfo($url, '/var/www/html');

?>
-----
<?php

use Drupal\Core\Database\Database;

// Two-arg call with string literal root: removed, leaving only $url
Database::convertDbUrlToConnectionInfo($url, '/var/www/html');

?>
