<?php

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;

// RECURSIVE_RENDER_LIMIT used as the true branch of a ternary.
$limit = $override ? EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT : 10;

// RECURSIVE_RENDER_LIMIT used as the false branch of a ternary.
$limit2 = $override ? 5 : EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT;

?>
-----
<?php

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;

// RECURSIVE_RENDER_LIMIT used as the true branch of a ternary.
$limit = $override ? \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => 20, fn() => EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT) : 10;

// RECURSIVE_RENDER_LIMIT used as the false branch of a ternary.
$limit2 = $override ? 5 : \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => 20, fn() => EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT);

?>
