<?php

node_add_body_field($this->getNodeType());
node_add_body_field($this->getNodeType(), 'Custom Label');

?>
-----
<?php

$this->createBodyField('node', $this->getNodeType()->id());
$this->createBodyField('node', $this->getNodeType()->id(), 'body', 'Custom Label');

?>
