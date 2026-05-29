<?php

/** @var \Drupal\Core\Entity\EntityTypeInterface[] $entity_types */
$entity_types['my_entity']->setUriCallback('my_entity_uri');
$entity_types['other']->setUriCallback('other_uri')->setLabel('Other');
$other->someMethod();

?>
-----
<?php

$entity_types['other']->setLabel('Other');
$other->someMethod();

?>
