<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// Only the $entity->original operand is rewritten; other operands stay in a
// residual unset().
unset($entity->original, $other->thing);
$keep = TRUE;
-----
<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// Only the $entity->original operand is rewritten; other operands stay in a
// residual unset().
unset($other->thing);
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $entity->setOriginal(NULL), fn() => $entity->original = NULL);
$keep = TRUE;
