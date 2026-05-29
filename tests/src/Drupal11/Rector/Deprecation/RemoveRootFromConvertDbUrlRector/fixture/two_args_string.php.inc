<?php

use Drupal\Core\Database\Database;

// Two-arg call with string literal root: removed, leaving only $url
Database::convertDbUrlToConnectionInfo($url, '/var/www/html');

?>
-----
<?php

use Drupal\Core\Database\Database;

// Two-arg call with string literal root: removed, leaving only $url
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => Database::convertDbUrlToConnectionInfo($url), fn() => Database::convertDbUrlToConnectionInfo($url, '/var/www/html'));

?>
