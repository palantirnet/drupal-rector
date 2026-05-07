<?php

use Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider;

class MyRouteProvider extends DefaultHtmlRouteProvider {
    public function doSomething($entity_type): void {
        if ($this->getEntityTypeIdKeyType($entity_type) === 'integer') {
            $result = 'integer';
        }
    }
}

?>
-----
<?php

use Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider;

class MyRouteProvider extends DefaultHtmlRouteProvider {
    public function doSomething($entity_type): void {
        if ($this->getEntityTypeIdKeyType($entity_type) === 'integer') {
            $result = 'integer';
        }
    }
}

?>
