<?php

// No type guard: any setPreviewMode(DRUPAL_*) call is transformed.
$anyObject->setPreviewMode(DRUPAL_DISABLED);
$anyObject->setPreviewMode(1);

?>
-----
<?php

// No type guard: any setPreviewMode(DRUPAL_*) call is transformed.
$anyObject->setPreviewMode(\Drupal\node\NodePreviewMode::Disabled);
$anyObject->setPreviewMode(\Drupal\node\NodePreviewMode::Optional);

?>
