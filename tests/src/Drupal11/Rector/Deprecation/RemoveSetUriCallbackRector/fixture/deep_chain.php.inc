<?php

// setUriCallback() mid-chain with leading fluent call: setX()->setUriCallback()->setY()
$entity_types['my_entity']->setLinkTemplate('canonical', '/my-entity/{my_entity}')->setUriCallback('my_entity_uri')->setLabel('My Entity');

?>
-----
<?php

// setUriCallback() mid-chain with leading fluent call: setX()->setUriCallback()->setY()
$entity_types['my_entity']->setLinkTemplate('canonical', '/my-entity/{my_entity}')->setLabel('My Entity');

?>
