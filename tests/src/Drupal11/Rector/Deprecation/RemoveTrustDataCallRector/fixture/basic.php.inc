<?php

/** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
$entity->trustData()->save();
$other->someMethod();

?>
-----
<?php

/** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
$entity->save();
$other->someMethod();

?>
