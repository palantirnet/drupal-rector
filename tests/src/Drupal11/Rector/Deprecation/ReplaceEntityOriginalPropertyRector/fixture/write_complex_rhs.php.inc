<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
$entity->original = $storage->load($entity->id());

?>
-----
<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
$entity->setOriginal($storage->load($entity->id()));

?>
