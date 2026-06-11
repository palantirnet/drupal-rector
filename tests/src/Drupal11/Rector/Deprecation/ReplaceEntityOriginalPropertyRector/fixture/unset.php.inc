<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// EntityBase::__unset('original') calls setOriginal(NULL). The < 11.2 deprecated
// path uses `$entity->original = NULL` because unset() is not an expression.
unset($entity->original);

?>
-----
<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// EntityBase::__unset('original') calls setOriginal(NULL). The < 11.2 deprecated
// path uses `$entity->original = NULL` because unset() is not an expression.
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $entity->setOriginal(NULL), fn() => $entity->original = NULL);

?>
