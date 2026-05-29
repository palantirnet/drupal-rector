<?php

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter as Formatter;

// Aliased import — Rector resolves the alias back to the FQCN.
$limit = Formatter::RECURSIVE_RENDER_LIMIT;

?>
-----
<?php

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter as Formatter;

// Aliased import — Rector resolves the alias back to the FQCN.
$limit = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => 20, fn() => Formatter::RECURSIVE_RENDER_LIMIT);

?>
