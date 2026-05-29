<?php

/** @var \Drupal\Core\Database\Connection $connection */
$connection->createConnectionOptionsFromUrl($url, $root);

\Drupal\Core\Database\Connection::createConnectionOptionsFromUrl($url, $root);
?>
-----
<?php

/** @var \Drupal\Core\Database\Connection $connection */
$connection->createConnectionOptionsFromUrl($url, NULL);

\Drupal\Core\Database\Connection::createConnectionOptionsFromUrl($url, NULL);
?>
