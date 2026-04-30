<?php

$names = node_type_get_names();
$label = node_get_type_label($node);

?>
-----
<?php

$names = \Drupal::service('entity_type.bundle.info')->getBundleLabels('node');
$label = $node->getBundleEntity()->label();

?>
