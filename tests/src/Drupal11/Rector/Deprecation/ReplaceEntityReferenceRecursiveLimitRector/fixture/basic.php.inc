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

if ($count > \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => 20, fn() => EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT)) {
    return;
}

$limit = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => 20, fn() => EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT);

?>
