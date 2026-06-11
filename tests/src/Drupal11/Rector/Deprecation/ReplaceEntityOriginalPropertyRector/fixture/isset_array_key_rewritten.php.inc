<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// Used as an array key, $entity->original is a normal expression slot (not the
// variable subject of isset()), so it is still rewritten.
$a = isset($map[$entity->original]);

?>
-----
<?php

/** @var \Drupal\Core\Entity\EntityInterface $entity */
// Used as an array key, $entity->original is a normal expression slot (not the
// variable subject of isset()), so it is still rewritten.
$a = isset($map[\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $entity->getOriginal(), fn() => $entity->original)]);

?>
