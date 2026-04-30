<?php

if ($this->getEntityTypeIdKeyType($entity_type) === 'integer') {
    $result = 'integer';
}
$a = $this->entityTypeSupportsComments($entity_type);
$b = $this->hasIntegerId($entity_type);

?>
-----
<?php

if ($entity_type->hasIntegerId()) {
    $result = 'integer';
}
$a = $entity_type->hasIntegerId();
$b = $entity_type->hasIntegerId();

?>
