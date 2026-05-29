<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
$original = $entity->original;
$entity->original = $unchanged;
$field = $entity->other_property;

?>
-----
<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
$original = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $entity->getOriginal(), fn() => $entity->original);
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $entity->setOriginal($unchanged), fn() => $entity->original = $unchanged);
$field = $entity->other_property;

?>
