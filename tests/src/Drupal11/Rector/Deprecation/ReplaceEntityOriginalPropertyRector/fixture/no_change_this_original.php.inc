<?php

// $this->original is preserved to avoid false positives on non-entity classes
// like EntityTypeEvent and FieldStorageDefinitionEvent that have a real $original property.
$value = $this->original;
$this->original = $other;

?>
-----
<?php

// $this->original is preserved to avoid false positives on non-entity classes
// like EntityTypeEvent and FieldStorageDefinitionEvent that have a real $original property.
$value = $this->original;
$this->original = $other;

?>
