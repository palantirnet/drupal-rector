<?php

$names = node_type_get_names();
$label = node_get_type_label($node);

?>
-----
<?php

$names = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service('entity_type.bundle.info')->getBundleLabels('node'), fn() => node_type_get_names());
$label = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $node->getBundleEntity()->label(), fn() => node_get_type_label($node));

?>
