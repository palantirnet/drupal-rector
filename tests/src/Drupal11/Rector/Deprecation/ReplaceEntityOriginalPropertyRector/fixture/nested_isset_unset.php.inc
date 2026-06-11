<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// A nested ->original is rewritten: isset($entity->getOriginal()->field) and
// unset($entity->getOriginal()->field) are valid PHP (only a bare method call
// as the outermost operand is fatal). The direct operand of a multi-operand
// isset() is left in place — isset($entity->getOriginal(), $other) would fatal.
$b = isset($entity->original->field);
unset($entity->original->field);
$c = isset($entity->original, $other);

?>
-----
<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// A nested ->original is rewritten: isset($entity->getOriginal()->field) and
// unset($entity->getOriginal()->field) are valid PHP (only a bare method call
// as the outermost operand is fatal). The direct operand of a multi-operand
// isset() is left in place — isset($entity->getOriginal(), $other) would fatal.
$b = isset(\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $entity->getOriginal(), fn() => $entity->original)->field);
unset(\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $entity->getOriginal(), fn() => $entity->original)->field);
$c = isset($entity->original, $other);

?>
