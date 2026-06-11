<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// empty() accepts an arbitrary expression, so the method-call rewrite is valid
// here (unlike isset()/unset()).
$a = empty($entity->original);

?>
-----
<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// empty() accepts an arbitrary expression, so the method-call rewrite is valid
// here (unlike isset()/unset()).
$a = empty(\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $entity->getOriginal(), fn() => $entity->original));

?>
