<?php

// Different string value: not 'integer', no transformation.
use Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider;

class MyRouteProvider extends DefaultHtmlRouteProvider {
    public function doSomething($entity_type): void {
        if ($this->getEntityTypeIdKeyType($entity_type) === 'string') {
            $result = 'string';
        }
    }
}

// Not called on $this: no transformation.
if ($other->getEntityTypeIdKeyType($entity_type) === 'integer') {
    $result = 'other';
}

// entityTypeSupportsComments without $this: no transformation.
$a = $other->entityTypeSupportsComments($entity_type);
