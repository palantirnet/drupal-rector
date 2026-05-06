<?php

\node_add_body_field($nodeType);
\node_add_body_field($nodeType, 'My Body');

?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $this->createBodyField('node', $nodeType->id()), fn() => \node_add_body_field($nodeType));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $this->createBodyField('node', $nodeType->id(), 'body', 'My Body'), fn() => \node_add_body_field($nodeType, 'My Body'));

?>
