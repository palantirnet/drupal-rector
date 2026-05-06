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
        if (\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $entity_type->hasIntegerId(), fn() => $this->getEntityTypeIdKeyType($entity_type) === 'integer')) {
            $result = 'integer';
        }
    }
}

?>
