<?php

use Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider;

// String on the left side of === is also transformed.
class MyRouteProvider extends DefaultHtmlRouteProvider {
    public function doSomething($entity_type): void {
        if ('integer' === $this->getEntityTypeIdKeyType($entity_type)) {
            $result = 'integer';
        }
    }
}

?>
-----
<?php

use Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider;

// String on the left side of === is also transformed.
class MyRouteProvider extends DefaultHtmlRouteProvider {
    public function doSomething($entity_type): void {
        if ($entity_type->hasIntegerId()) {
            $result = 'integer';
        }
    }
}

?>
