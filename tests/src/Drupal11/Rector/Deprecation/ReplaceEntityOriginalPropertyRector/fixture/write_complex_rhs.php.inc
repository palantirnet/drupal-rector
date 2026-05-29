<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
$entity->original = $storage->load($entity->id());

?>
-----
<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $entity->setOriginal($storage->load($entity->id())), fn() => $entity->original = $storage->load($entity->id()));

?>
