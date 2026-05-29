<?php

// $this in an unrelated class — PHPStan cannot confirm the owning class, so no change.
class UnrelatedClass {
    public function doSomething($entity_type): void {
        $a = $this->entityTypeSupportsComments($entity_type);
        $b = $this->hasIntegerId($entity_type);
        if ($this->getEntityTypeIdKeyType($entity_type) === 'integer') {}
    }
}
