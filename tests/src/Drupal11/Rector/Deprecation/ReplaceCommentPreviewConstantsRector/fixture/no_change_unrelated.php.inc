<?php

// Unrelated class — type guard prevents transformation.
/** @var \SomeOtherClass $obj */
$obj->setCommentPreview(DRUPAL_DISABLED);

// Different method on a CommentTestBase — must not change.
/** @var \Drupal\Tests\comment\Functional\CommentTestBase $base */
$base->setCommentSettings('preview', DRUPAL_DISABLED, '', 'comment');

// First arg is a different constant — must not change.
$base->setCommentPreview(SOME_OTHER_CONSTANT);

// First arg is a literal int — must not change (rector only handles named constants).
$base->setCommentPreview(0);
?>
-----
<?php

// Unrelated class — type guard prevents transformation.
/** @var \SomeOtherClass $obj */
$obj->setCommentPreview(DRUPAL_DISABLED);

// Different method on a CommentTestBase — must not change.
/** @var \Drupal\Tests\comment\Functional\CommentTestBase $base */
$base->setCommentSettings('preview', DRUPAL_DISABLED, '', 'comment');

// First arg is a different constant — must not change.
$base->setCommentPreview(SOME_OTHER_CONSTANT);

// First arg is a literal int — must not change (rector only handles named constants).
$base->setCommentPreview(0);
?>
