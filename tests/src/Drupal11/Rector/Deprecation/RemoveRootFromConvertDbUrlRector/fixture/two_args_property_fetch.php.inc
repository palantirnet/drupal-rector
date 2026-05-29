<?php

use Drupal\Core\Database\Database;

// Two-arg call where second arg is a property fetch (no third arg)
Database::convertDbUrlToConnectionInfo($url, $this->root);

?>
-----
<?php

use Drupal\Core\Database\Database;

// Two-arg call where second arg is a property fetch (no third arg)
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => Database::convertDbUrlToConnectionInfo($url), fn() => Database::convertDbUrlToConnectionInfo($url, $this->root));

?>
