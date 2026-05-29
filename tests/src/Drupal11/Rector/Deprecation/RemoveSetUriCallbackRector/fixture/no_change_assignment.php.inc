<?php

// Assignment context: $x = $et->setUriCallback(...) is not handled because the
// rector only removes standalone Expression statements and mid-chain calls.
// The assignment wrapper prevents matching.
$x = $entity_type->setUriCallback('my_entity_uri');
