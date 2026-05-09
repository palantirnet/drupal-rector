<?php

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;

// RECURSIVE_RENDER_LIMIT used as a function argument.
$capped = min($count, EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT);

// RECURSIVE_RENDER_LIMIT used as a second argument.
$result = some_check($id, EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT);

?>
-----
<?php

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;

// RECURSIVE_RENDER_LIMIT used as a function argument.
$capped = min($count, \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => 20, fn() => EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT));

// RECURSIVE_RENDER_LIMIT used as a second argument.
$result = some_check($id, \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => 20, fn() => EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT));

?>
