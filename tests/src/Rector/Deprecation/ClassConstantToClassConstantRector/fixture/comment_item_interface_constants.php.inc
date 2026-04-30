<?php

use Drupal\comment\Plugin\Field\FieldType\CommentItemInterface;
use Drupal\comment\CommentInterface;

$status = CommentItemInterface::HIDDEN;
$closed = CommentItemInterface::CLOSED;
$open = CommentItemInterface::OPEN;
$forbidden = CommentInterface::ANONYMOUS_MAYNOT_CONTACT;
$allowed = CommentInterface::ANONYMOUS_MAY_CONTACT;
$required = CommentInterface::ANONYMOUS_MUST_CONTACT;
?>
-----
<?php

use Drupal\comment\Plugin\Field\FieldType\CommentItemInterface;
use Drupal\comment\CommentInterface;

$status = \Drupal\comment\CommentingStatus::Hidden;
$closed = \Drupal\comment\CommentingStatus::Closed;
$open = \Drupal\comment\CommentingStatus::Open;
$forbidden = \Drupal\comment\AnonymousContact::Forbidden;
$allowed = \Drupal\comment\AnonymousContact::Allowed;
$required = \Drupal\comment\AnonymousContact::Required;
?>
