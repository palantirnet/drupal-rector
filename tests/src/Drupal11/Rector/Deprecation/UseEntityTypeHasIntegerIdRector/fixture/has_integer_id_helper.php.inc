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
        $b = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $entity_type->hasIntegerId(), fn() => $this->hasIntegerId($entity_type));
    }
}

?>
