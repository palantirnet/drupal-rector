<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// EntityBase::__isset('original') returns getOriginal(), so the equivalent
// check is getOriginal() !== NULL.
$a = isset($entity->original);

?>
-----
<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// EntityBase::__isset('original') returns getOriginal(), so the equivalent
// check is getOriginal() !== NULL.
$a = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $entity->getOriginal() !== NULL, fn() => isset($entity->original));

?>
