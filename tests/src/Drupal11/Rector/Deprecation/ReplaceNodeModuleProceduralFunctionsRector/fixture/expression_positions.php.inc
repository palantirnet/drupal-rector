<?php

$names = node_type_get_names();
if (node_type_get_names()) {
}
$label = node_get_type_label($this->getNode());

?>
-----
<?php

$names = \Drupal::service('entity_type.bundle.info')->getBundleLabels('node');
if (\Drupal::service('entity_type.bundle.info')->getBundleLabels('node')) {
}
$label = $this->getNode()->getBundleEntity()->label();

?>
