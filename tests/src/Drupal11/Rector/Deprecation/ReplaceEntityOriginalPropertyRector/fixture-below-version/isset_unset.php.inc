<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// Below the introduced version (11.2.0) nothing is rewritten.
$a = isset($entity->original);
unset($entity->original);

?>
-----
<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// Below the introduced version (11.2.0) nothing is rewritten.
$a = isset($entity->original);
unset($entity->original);

?>
