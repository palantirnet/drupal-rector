<?php

$desc = node_type_get_description($node_type);

?>
-----
<?php

$desc = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $node_type->getDescription(), fn() => node_type_get_description($node_type));

?>
