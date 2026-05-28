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

$status = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\comment\CommentingStatus::Hidden, fn() => CommentItemInterface::HIDDEN);
$closed = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\comment\CommentingStatus::Closed, fn() => CommentItemInterface::CLOSED);
$open = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\comment\CommentingStatus::Open, fn() => CommentItemInterface::OPEN);
$forbidden = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\comment\AnonymousContact::Forbidden, fn() => CommentInterface::ANONYMOUS_MAYNOT_CONTACT);
$allowed = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\comment\AnonymousContact::Allowed, fn() => CommentInterface::ANONYMOUS_MAY_CONTACT);
$required = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\comment\AnonymousContact::Required, fn() => CommentInterface::ANONYMOUS_MUST_CONTACT);
?>
