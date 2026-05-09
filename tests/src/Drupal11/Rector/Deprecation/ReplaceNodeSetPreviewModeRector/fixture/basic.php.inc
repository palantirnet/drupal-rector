<?php

/** @var \Drupal\node\NodeTypeInterface $nodeType */
$nodeType->setPreviewMode(DRUPAL_DISABLED);
$nodeType->setPreviewMode(DRUPAL_OPTIONAL);
$nodeType->setPreviewMode(DRUPAL_REQUIRED);
$nodeType->setPreviewMode(0);
$nodeType->setPreviewMode(1);
$nodeType->setPreviewMode(2);

?>
-----
<?php

/** @var \Drupal\node\NodeTypeInterface $nodeType */
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $nodeType->setPreviewMode(\Drupal\node\NodePreviewMode::Disabled), fn() => $nodeType->setPreviewMode(DRUPAL_DISABLED));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $nodeType->setPreviewMode(\Drupal\node\NodePreviewMode::Optional), fn() => $nodeType->setPreviewMode(DRUPAL_OPTIONAL));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $nodeType->setPreviewMode(\Drupal\node\NodePreviewMode::Required), fn() => $nodeType->setPreviewMode(DRUPAL_REQUIRED));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $nodeType->setPreviewMode(\Drupal\node\NodePreviewMode::Disabled), fn() => $nodeType->setPreviewMode(0));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $nodeType->setPreviewMode(\Drupal\node\NodePreviewMode::Optional), fn() => $nodeType->setPreviewMode(1));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $nodeType->setPreviewMode(\Drupal\node\NodePreviewMode::Required), fn() => $nodeType->setPreviewMode(2));

?>
