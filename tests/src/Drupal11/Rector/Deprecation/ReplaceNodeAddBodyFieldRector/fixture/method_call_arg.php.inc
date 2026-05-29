<?php

node_add_body_field($this->getNodeType());
node_add_body_field($this->getNodeType(), 'Custom Label');

?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $this->createBodyField('node', $this->getNodeType()->id()), fn() => node_add_body_field($this->getNodeType()));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $this->createBodyField('node', $this->getNodeType()->id(), 'body', 'Custom Label'), fn() => node_add_body_field($this->getNodeType(), 'Custom Label'));

?>
