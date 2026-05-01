<?php

// getPreviewMode() comparisons and the $returnAsInt parameter are out of
// scope for this rector; they are not transformed.
if ($nodeType->getPreviewMode() === DRUPAL_DISABLED) {
}
$mode = $nodeType->getPreviewMode(TRUE);

?>
-----
<?php

// getPreviewMode() comparisons and the $returnAsInt parameter are out of
// scope for this rector; they are not transformed.
if ($nodeType->getPreviewMode() === DRUPAL_DISABLED) {
}
$mode = $nodeType->getPreviewMode(TRUE);

?>
