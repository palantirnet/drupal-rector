<?php

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;

// self:: and static:: inside a subclass of EntityReferenceEntityFormatter are
// NOT transformed because isName() on a self/static Name node returns the keyword
// itself, not the resolved FQCN. This is a known limitation — the rector only
// matches explicit class names.
class CustomFormatter extends EntityReferenceEntityFormatter {
    public function check(int $count): bool {
        if ($count > self::RECURSIVE_RENDER_LIMIT) {
            return false;
        }
        if ($count > static::RECURSIVE_RENDER_LIMIT) {
            return false;
        }
        return true;
    }
}
