<?php

use Drupal\layout_builder\Plugin\SectionStorage\OverridesSectionStorage;

class MyStorage extends OverridesSectionStorage {
    public function doSomething($entity_type): void {
        $b = $this->hasIntegerId($entity_type);
    }
}

?>
-----
<?php

use Drupal\layout_builder\Plugin\SectionStorage\OverridesSectionStorage;

class MyStorage extends OverridesSectionStorage {
    public function doSomething($entity_type): void {
        $b = $entity_type->hasIntegerId();
    }
}

?>
