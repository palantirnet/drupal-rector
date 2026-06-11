<?php

namespace Drupal\mymodule\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Attribute\EntityReferenceSelection;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[EntityReferenceSelection(
    id: "mymodule:block_content",
    label: new TranslatableMarkup("My block content selection"),
    group: "mymodule",
    entity_types: ["block_content"],
)]
class MyBlockContentSelection extends DefaultSelection
{
}

?>
-----
<?php

namespace Drupal\mymodule\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Attribute\EntityReferenceSelection;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[EntityReferenceSelection(
    id: "mymodule:block_content",
    label: new TranslatableMarkup("My block content selection"),
    group: "mymodule",
    entity_types: ["block_content"],
)]
class MyBlockContentSelection extends \Drupal\block_content\Plugin\EntityReferenceSelection\BlockContentSelection
{
}

?>
