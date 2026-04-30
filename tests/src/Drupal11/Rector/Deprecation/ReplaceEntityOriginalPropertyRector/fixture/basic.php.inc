<?php

$original = $entity->original;
$entity->original = $unchanged;
$field = $entity->other_property;

?>
-----
<?php

$original = $entity->getOriginal();
$entity->setOriginal($unchanged);
$field = $entity->other_property;

?>
