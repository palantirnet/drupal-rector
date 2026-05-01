<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
$id = $entity->original->id();

?>
-----
<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
$id = $entity->getOriginal()->id();

?>
