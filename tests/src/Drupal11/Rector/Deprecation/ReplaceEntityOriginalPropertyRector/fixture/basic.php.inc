<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
$original = $entity->original;
$entity->original = $unchanged;
$field = $entity->other_property;

?>
-----
<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
$original = $entity->getOriginal();
$entity->setOriginal($unchanged);
$field = $entity->other_property;

?>
