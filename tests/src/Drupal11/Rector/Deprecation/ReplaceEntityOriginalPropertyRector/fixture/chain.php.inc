<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
$id = $entity->original->id();

?>
-----
<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
$id = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $entity->getOriginal(), fn() => $entity->original)->id();

?>
