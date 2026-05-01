<?php

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;

// parent:: inside a subclass override is NOT transformed — the rector only
// matches explicit class names (short or FQCN), not the 'parent' keyword.
class CustomFormatter extends EntityReferenceEntityFormatter {
    const RECURSIVE_RENDER_LIMIT = parent::RECURSIVE_RENDER_LIMIT;
}
