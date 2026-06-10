<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// Nested operands have no clean isset()/unset() equivalent — isset($entity->getOriginal()->field)
// is a fatal — and multi-operand isset() is left intact, so these are not rewritten.
// The inner ->original is also left in place rather than turned into a method call.
$b = isset($entity->original->field);
unset($entity->original->field);
$c = isset($entity->original, $other);

?>
-----
<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// Nested operands have no clean isset()/unset() equivalent — isset($entity->getOriginal()->field)
// is a fatal — and multi-operand isset() is left intact, so these are not rewritten.
// The inner ->original is also left in place rather than turned into a method call.
$b = isset($entity->original->field);
unset($entity->original->field);
$c = isset($entity->original, $other);

?>
