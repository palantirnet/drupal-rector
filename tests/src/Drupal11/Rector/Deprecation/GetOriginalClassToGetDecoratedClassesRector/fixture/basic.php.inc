<?php

/** @var \Drupal\Core\Entity\EntityTypeInterface $entityType */
$originalClass = $entityType->getOriginalClass();
?>
-----
<?php

/** @var \Drupal\Core\Entity\EntityTypeInterface $entityType */
$originalClass = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $entityType->getDecoratedClasses()[0], fn() => $entityType->getOriginalClass());
?>
