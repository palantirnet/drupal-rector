<?php

/** @var \Drupal\Core\Entity\EntityInterface|null $entity */
$id = $entity?->original?->id();

?>
-----
<?php

/** @var \Drupal\Core\Entity\EntityInterface|null $entity */
$id = $entity?->getOriginal()?->id();

?>
