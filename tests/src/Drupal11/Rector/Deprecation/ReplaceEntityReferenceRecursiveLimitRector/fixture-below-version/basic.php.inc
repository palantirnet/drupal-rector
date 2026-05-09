<?php

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;

if ($count > EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT) {
    return;
}

$limit = EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT;

?>
-----
<?php

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;

if ($count > EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT) {
    return;
}

$limit = EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT;

?>
