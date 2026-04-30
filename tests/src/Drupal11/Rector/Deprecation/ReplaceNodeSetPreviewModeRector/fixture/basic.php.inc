<?php

$nodeType->setPreviewMode(DRUPAL_DISABLED);
$nodeType->setPreviewMode(DRUPAL_OPTIONAL);
$nodeType->setPreviewMode(DRUPAL_REQUIRED);
$nodeType->setPreviewMode(0);
$nodeType->setPreviewMode(1);
$nodeType->setPreviewMode(2);

?>
-----
<?php

$nodeType->setPreviewMode(\Drupal\node\NodePreviewMode::Disabled);
$nodeType->setPreviewMode(\Drupal\node\NodePreviewMode::Optional);
$nodeType->setPreviewMode(\Drupal\node\NodePreviewMode::Required);
$nodeType->setPreviewMode(\Drupal\node\NodePreviewMode::Disabled);
$nodeType->setPreviewMode(\Drupal\node\NodePreviewMode::Optional);
$nodeType->setPreviewMode(\Drupal\node\NodePreviewMode::Required);

?>
