<?php

use Drupal\Core\Entity\EntityTypeInterface;

// Grandchild of the interface (via BaseEntityType) is also transformed.
class BaseEntityType implements EntityTypeInterface {}
class DerivedEntityType extends BaseEntityType {}

/** @var DerivedEntityType $entity_type */
$entity_type->setUriCallback('my_entity_uri');

?>
-----
<?php

use Drupal\Core\Entity\EntityTypeInterface;

// Grandchild of the interface (via BaseEntityType) is also transformed.
class BaseEntityType implements EntityTypeInterface {}
class DerivedEntityType extends BaseEntityType {}

?>
