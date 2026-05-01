<?php

$unrelated_object->setUriCallback('my_entity_uri');
// No type guard: the call above is still removed even though the receiver is
// not an EntityTypeInterface instance. The method name is unique in Drupal core
// so false positives are extremely unlikely.
$unrelated_object->someOtherMethod();

?>
-----
<?php

// No type guard: the call above is still removed even though the receiver is
// not an EntityTypeInterface instance. The method name is unique in Drupal core
// so false positives are extremely unlikely.
$unrelated_object->someOtherMethod();

?>
