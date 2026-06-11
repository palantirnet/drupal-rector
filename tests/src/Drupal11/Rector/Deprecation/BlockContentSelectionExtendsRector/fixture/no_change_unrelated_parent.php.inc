<?php

// A block_content selection plugin that does NOT extend DefaultSelection — the
// parent-class gate does not match, so it is left untouched.

namespace Drupal\mymodule\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Attribute\EntityReferenceSelection;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[EntityReferenceSelection(
    id: "mymodule:block_content",
    label: new TranslatableMarkup("My block content selection"),
    group: "mymodule",
    entity_types: ["block_content"],
)]
class MyBlockContentSelection extends \Drupal\some_module\SomeOtherBase
{
}

?>
-----
<?php

// A block_content selection plugin that does NOT extend DefaultSelection — the
// parent-class gate does not match, so it is left untouched.

namespace Drupal\mymodule\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Attribute\EntityReferenceSelection;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[EntityReferenceSelection(
    id: "mymodule:block_content",
    label: new TranslatableMarkup("My block content selection"),
    group: "mymodule",
    entity_types: ["block_content"],
)]
class MyBlockContentSelection extends \Drupal\some_module\SomeOtherBase
{
}

?>
