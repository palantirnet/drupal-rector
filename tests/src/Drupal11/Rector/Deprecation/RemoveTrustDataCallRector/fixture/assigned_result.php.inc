<?php

// Result of trustData() assigned to a variable (expression context).
/** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
$entity2 = $entity->trustData();

?>
-----
<?php

// Result of trustData() assigned to a variable (expression context).
/** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
$entity2 = $entity;

?>
