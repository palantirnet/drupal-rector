<?php

// The canonical core BlockContentSelection extends DefaultSelection and carries
// the same block_content attribute, but must not be rewritten to extend itself.

namespace Drupal\block_content\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Attribute\EntityReferenceSelection;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[EntityReferenceSelection(
    id: "default:block_content",
    label: new TranslatableMarkup("Block content selection"),
    group: "default",
    weight: 1,
    entity_types: ["block_content"],
)]
class BlockContentSelection extends DefaultSelection
{
}

?>
-----
<?php

// The canonical core BlockContentSelection extends DefaultSelection and carries
// the same block_content attribute, but must not be rewritten to extend itself.

namespace Drupal\block_content\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Attribute\EntityReferenceSelection;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[EntityReferenceSelection(
    id: "default:block_content",
    label: new TranslatableMarkup("Block content selection"),
    group: "default",
    weight: 1,
    entity_types: ["block_content"],
)]
class BlockContentSelection extends DefaultSelection
{
}

?>
