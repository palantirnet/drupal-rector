<?php

// Different string value: not 'integer', no transformation.
if ($this->getEntityTypeIdKeyType($entity_type) === 'string') {
    $result = 'string';
}

// Not called on $this: no transformation.
if ($other->getEntityTypeIdKeyType($entity_type) === 'integer') {
    $result = 'other';
}

// entityTypeSupportsComments without $this: no transformation.
$a = $other->entityTypeSupportsComments($entity_type);

?>
-----
<?php

// Different string value: not 'integer', no transformation.
if ($this->getEntityTypeIdKeyType($entity_type) === 'string') {
    $result = 'string';
}

// Not called on $this: no transformation.
if ($other->getEntityTypeIdKeyType($entity_type) === 'integer') {
    $result = 'other';
}

// entityTypeSupportsComments without $this: no transformation.
$a = $other->entityTypeSupportsComments($entity_type);

?>
