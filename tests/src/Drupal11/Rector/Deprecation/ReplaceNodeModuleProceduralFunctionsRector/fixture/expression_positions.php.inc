<?php

$names = node_type_get_names();
if (node_type_get_names()) {
}
$label = node_get_type_label($this->getNode());

?>
-----
<?php

$names = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service('entity_type.bundle.info')->getBundleLabels('node'), fn() => node_type_get_names());
if (\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service('entity_type.bundle.info')->getBundleLabels('node'), fn() => node_type_get_names())) {
}
$label = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $this->getNode()->getBundleEntity()->label(), fn() => node_get_type_label($this->getNode()));

?>
