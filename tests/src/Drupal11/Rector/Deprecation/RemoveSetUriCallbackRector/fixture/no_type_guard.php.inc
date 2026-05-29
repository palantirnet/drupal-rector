<?php

// Untyped caller — PHPStan cannot confirm EntityTypeInterface, so no change.
$unrelated_object->setUriCallback('my_entity_uri');
$unrelated_object->someOtherMethod();
