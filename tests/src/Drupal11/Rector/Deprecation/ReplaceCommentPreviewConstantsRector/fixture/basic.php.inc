<?php

/** @var \Drupal\Tests\comment\Functional\CommentTestBase $base */
$base->setCommentPreview(DRUPAL_DISABLED);
$base->setCommentPreview(DRUPAL_OPTIONAL);
$base->setCommentPreview(DRUPAL_REQUIRED);
$base->setCommentPreview(DRUPAL_DISABLED, 'field_comments');
?>
-----
<?php

/** @var \Drupal\Tests\comment\Functional\CommentTestBase $base */
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $base->setCommentPreview(\Drupal\comment\CommentPreviewMode::Disabled), fn() => $base->setCommentPreview(DRUPAL_DISABLED));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $base->setCommentPreview(\Drupal\comment\CommentPreviewMode::Optional), fn() => $base->setCommentPreview(DRUPAL_OPTIONAL));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $base->setCommentPreview(\Drupal\comment\CommentPreviewMode::Required), fn() => $base->setCommentPreview(DRUPAL_REQUIRED));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $base->setCommentPreview(\Drupal\comment\CommentPreviewMode::Disabled, 'field_comments'), fn() => $base->setCommentPreview(DRUPAL_DISABLED, 'field_comments'));
?>
