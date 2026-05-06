<?php

/** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
$entity->trustData()->save();
$other->someMethod();

?>
-----
<?php

/** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $entity, fn() => $entity->trustData())->save();
$other->someMethod();

?>
