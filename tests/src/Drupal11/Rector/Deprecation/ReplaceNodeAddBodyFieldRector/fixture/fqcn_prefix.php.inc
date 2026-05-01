<?php

\node_add_body_field($nodeType);
\node_add_body_field($nodeType, 'My Body');

?>
-----
<?php

$this->createBodyField('node', $nodeType->id());
$this->createBodyField('node', $nodeType->id(), 'body', 'My Body');

?>
