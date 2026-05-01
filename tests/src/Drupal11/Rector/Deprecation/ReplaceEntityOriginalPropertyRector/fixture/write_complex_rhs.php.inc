<?php

$entity->original = $storage->load($entity->id());

?>
-----
<?php

$entity->setOriginal($storage->load($entity->id()));

?>
