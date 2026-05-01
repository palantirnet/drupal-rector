<?php

// String on the left side of === is also transformed.
if ('integer' === $this->getEntityTypeIdKeyType($entity_type)) {
    $result = 'integer';
}

?>
-----
<?php

// String on the left side of === is also transformed.
if ($entity_type->hasIntegerId()) {
    $result = 'integer';
}

?>
