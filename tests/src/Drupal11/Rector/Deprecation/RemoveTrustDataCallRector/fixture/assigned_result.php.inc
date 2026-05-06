<?php

// Result of trustData() assigned to a variable (expression context).
/** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
$entity2 = $entity->trustData();

?>
-----
<?php

// Result of trustData() assigned to a variable (expression context).
/** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
$entity2 = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $entity, fn() => $entity->trustData());

?>
