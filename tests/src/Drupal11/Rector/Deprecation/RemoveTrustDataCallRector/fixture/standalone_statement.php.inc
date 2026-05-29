<?php

// trustData() called as a standalone statement (not chained).
/** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
$entity->trustData();

?>
-----
<?php

// trustData() called as a standalone statement (not chained).
/** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $entity, fn() => $entity->trustData());

?>
