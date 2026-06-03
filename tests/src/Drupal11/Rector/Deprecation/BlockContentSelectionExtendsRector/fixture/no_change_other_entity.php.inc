<?php

// A DefaultSelection subclass that targets a different entity type — the
// block_content gate does not match, so it is left untouched.

namespace Drupal\mymodule\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Attribute\EntityReferenceSelection;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[EntityReferenceSelection(
    id: "mymodule:node",
    label: new TranslatableMarkup("My node selection"),
    group: "mymodule",
    entity_types: ["node"],
)]
class MyNodeSelection extends DefaultSelection
{
}

?>
-----
<?php

// A DefaultSelection subclass that targets a different entity type — the
// block_content gate does not match, so it is left untouched.

namespace Drupal\mymodule\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Attribute\EntityReferenceSelection;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[EntityReferenceSelection(
    id: "mymodule:node",
    label: new TranslatableMarkup("My node selection"),
    group: "mymodule",
    entity_types: ["node"],
)]
class MyNodeSelection extends DefaultSelection
{
}

?>
