<?php

// trustData() called as a standalone statement (not chained).
/** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
$entity->trustData();

?>
-----
<?php

// trustData() called as a standalone statement (not chained).
/** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
$entity;

?>
