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
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => Database::convertDbUrlToConnectionInfo($url), fn() => Database::convertDbUrlToConnectionInfo($url, $this->getRoot()));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => Database::convertDbUrlToConnectionInfo($url, TRUE), fn() => Database::convertDbUrlToConnectionInfo($url, $this->getDrupalRoot(), TRUE));

?>
